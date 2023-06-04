<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Handling;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use App\Models\Profile;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // return Order::with('categories')->with('user.profile')->with('categories.parent')->get();
        $query = Order::query();
        return $query->has('categories')->with('user.profile')->with('categories.parent')->get();
        return OrderResource::collection($this->paginated($query, $request));
    }

    public function orders(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $categories = $request->body['garments'];

            $handle = $request->body['handling'];
            $serbesyo = $request->body['service'];
            $personalInfo = $request->body['personal_details'];
            $paymentMethod = $request->body['payment_method'];


            $user = User::updateOrCreate([
                'email' => $personalInfo['email'],
                'role' => 'Customer',
            ]);

            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->first_name = $personalInfo['firstname'];
            $profile->last_name = $personalInfo['lastname'];
            $profile->land_mark = $personalInfo['landmark'];
            $profile->purok = $personalInfo['purok'];
            $profile->brgy = $personalInfo['brgy'];
            $profile->municipality = $personalInfo['municipality'];
            $profile->contact_number = $personalInfo['phone'];
            $profile->save();


            $handling = Handling::where('handling_name', $handle)->first();
            // $servicing = Service::where('service_name', $serbesyo)->first();
            $payment = Payment::where('payment_name', $paymentMethod)->first();

            $service = Service::where('service_name', $serbesyo)->first();

            $transNumber = Order::generateTransactionNumber();
            $newOrder = Order::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'handling_id' => $handling->id,
                'trans_number' => $transNumber,
                'service_id' => $service->id,
            ]);

            foreach ($categories as $key => $value) {
                if (!empty($value)) {
                    $category = Category::where('name', $key)->first();
                    if ($category) {
                        $parent_id = $category->parent_id;
                        $id = $category->id;
                        $user->categories;
                        $user->categories()->attach($parent_id, [
                            'order_id' => $newOrder->id,
                            'user_id' => $user->id,
                            'category_id' => $id,
                            'quantity' => $value,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => "Order Successfully added!"
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function view($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Handling not found',
            ], 500);
        }

        return response()->json($order, 200);
    }

    public function manualOrder(Request $request){
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            if (!$order) {
                throw new Exception('Order not found');
            }

            // Update the order fields based on the request data
            $order->payment_status = $request->input('payment_status');
            $order->status = $request->input('status');
            $order->total = $request->input('total');
            $order->ref_num = $request->input('ref_num');
            $order->change = $request->input('change');
            $order->amount= $request->input('amount');
            $order->fabcon = $request->input('fabcon');
            $order->detergent= $request->input('detergent');
            $order->approved_by = $request->input('approved_by');

            // Save the changes to the order
        $order->save();

        // Update the categories and their quantities if provided
        $categories = $request->input('categories', []);
        foreach ($categories as $categoryId => $quantity) {
            $category = Category::find($categoryId);
            if ($category) {
                // Update the quantity in the pivot table
                $order->categories()->updateExistingPivot($category->id, [
                    'quantity' => $quantity,
                ]);
            }
        }

        DB::commit();
        // return response()->json([
        //     'status' => 200,
        //     'message' => 'Order successfully updated!',
        // ]);
        return new OrderResource($newOrder); 
    } catch (Exception $e) {
        DB::rollback();
        return response()->json([
            'status' => 500,
            'message' => $e->getMessage(),
        ]);
    }
    }

        public function updateStatus(Request $request, $id)
        {
            $order = Order::findOrFail($id);

            $newStatus = $request->input('status');

            if ($newStatus === 'in progress' && $order->status === 'pending') {
                $order->status = 'in progress';
                $order->save();
            } elseif ($newStatus === 'completed' && $order->status === 'in progress') {
                $order->status = 'completed';
                $order->save();

               
            }

            return response()->json(['message' => 'Order status updated successfully']);
        }


        public function updatePaymentStatus(Request $request, $id)
        {
            $order = Order::findOrFail($id);
        $id = auth()->user();
        $profile = Profile::where('user_id', $id->id)->first();

            $newPaymentStatus = $request->input('payment_status');

            if ($newPaymentStatus === 'paid' && $order->payment_status === 'unpaid') {
            // $order->status = 'completed';
                $order->payment_status = 'paid';
            $order->approved_by = $profile ? ($profile->first_name . ' ' . $profile->last_name) : "Admin Admin";
                $order->save();

                  // Retrieve the user who made the request
                  $user = Auth::user()->first_name;

                  // Assign the user's name to the approved_by field
                  $order->approved_by = $user->first_name;
                  $order->save();
            }

            return response()->json(['message' => 'Order status updated successfully']);
        }

        // input kilo
        public function updateKilo(Request $request, $id)
        {
            $order = Order::findOrFail($id);
        
            $newKilo = $request->input('kilo');
        
            // Update the kilo in the pivot table for each category
            foreach ($order->categories as $category) {
                $order->categories()->updateExistingPivot($category->id, [
                    'kilo' => $newKilo,
                ]);
            }
        
            return response()->json(['message' => 'Kilo updated successfully']);
        }

      

        public function updateTotal(Request $request, $id)
        {
            $order = Order::findOrFail($id);
        
            $validatedData = $request->validate([
                'total' => 'required|numeric',
            ]);
        
            $order->total = $validatedData['total'];
            $order->save();
        
            return response()->json(['message' => 'Total value updated successfully']);
        }

        public function updateAmount(Request $request, $id)
        {
            $order = Order::findOrFail($id);
        
            $validatedData = $request->validate([
                'amount' => 'required|numeric',
            ]);
        
            $order->amount = $validatedData['amount'];
            $order->save();
        
            return response()->json(['message' => 'Amount value updated successfully']);
        }

        public function updateChange(Request $request, $id)
        {
            $order = Order::findOrFail($id);
        
            $validatedData = $request->validate([
                'change' => 'required|numeric',
            ]);
        
            $order->change = $validatedData['change'];
            $order->save();
        
            return response()->json(['message' => 'Change value updated successfully']);
        }

        public function updateRefNum(Request $request, $id)
        {
            $order = Order::findOrFail($id);
        
            $validatedData = $request->validate([
                'ref_num' => 'required|numeric',
            ]);
        
            $order->ref_num = $validatedData['ref_num'];
            $order->save();
        
            return response()->json(['message' => 'Reference# value updated successfully']);
        }

        public function updateFabcon(Request $request, $orderId)
        {
            $order = Order::findOrFail($orderId);
            $order->fabcon_id = $request->input('fabcon_id');
            $order->save();
        
            return response()->json(['message' => 'Fabcon updated successfully']);
        }
        
   

        public function destroy($id)
        {
            DB::beginTransaction();
            try {
                $order = Order::find($id);
        
                if (!$order) {
                    throw new Exception('Order not found');
                }
        
                $order->categories()->detach(); // Remove the category associations
                $order->delete(); // Delete the order
        
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Order deleted successfully!',
                ]);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }
        
        

 }