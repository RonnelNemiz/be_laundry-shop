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

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Order::with('categories')->with('user.profile')->with('categories.parent')->get();
        // $query = Order::query();
        // return $query->has('categories')->with('user.profile')->with('categories.parent')->get();
        // return OrderResource::collection($this->paginated($query, $request));
    }

    public function orders(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $categories = $request->body['garments'];

            $handle = $request->body['handling'];
            $personalInfo = $request->body['personal_details'];
            $paymentMethod = $request->body['payment_method'];

            $user = User::updateOrCreate([
                'email' => $personalInfo['email'],
                'role' => 'Customer',
            ]);

            $profile = new Profile();
            $profile->user_id = $user->id;
            // $profile->first_name = $personalInfo->firstname;
            // $profile->last_name = $personalInfo->lastname;
            // $profile->land_mark = $personalInfo->landmark;
            // $profile->purok = $personalInfo->purok;
            // $profile->brgy = $personalInfo->brgy;
            // $profile->municipality = $personalInfo->municipality;
            // $profile->contact_number = $personalInfo->phone;
            $profile->first_name = $personalInfo['firstname'];
            $profile->last_name = $personalInfo['lastname'];
            $profile->land_mark = $personalInfo['landmark'];
            $profile->purok = $personalInfo['purok'];
            $profile->brgy = $personalInfo['brgy'];
            $profile->municipality = $personalInfo['municipality'];
            $profile->contact_number = $personalInfo['phone'];
            $profile->save();


            $handling = Handling::where('handling_name', $handle)->first();
            $payment = Payment::where('payment_name', $paymentMethod)->first();

            $transNumber = Order::generateTransactionNumber();
            $newOrder = Order::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'handling_id' => $handling->id,
                'trans_number' => $transNumber,
                // 'payment_status' => $payment_status,
                // 'status' => $status,
                // 'total' => $total,
                // 'approved_by' => $approved_by,
                // 'created_at' => $created_at,
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
        return response()->json([
            'status' => 200,
            'message' => 'Order successfully updated!',
        ]);
    } catch (Exception $e) {
        DB::rollback();
        return response()->json([
            'status' => 500,
            'message' => $e->getMessage(),
        ]);
    }
    }

    // public function updateStatus(Request $request, $orderId)
    // {
    //     $validatedData = $request->validate([
    //         'status' => 'required|in:inprogress,completed',
    //     ]);

    //     $order = Order::find($orderId);

    //     if (!$order) {
    //         return response()->json(['error' => 'Order not found'], 500);
    //     }

    //     $order->status = $validatedData['status'];
    //     $order->save();

    //     return response()->json(['message' => 'Order status updated successfully']);
    // }
   
    // change status
//     public function changeOrderStatus(Request $request)
// {
//     DB::beginTransaction();
//     try {
//         $orderId = $request->route('id'); 
//         $status = $request->input('status');

//         $order = Order::find($orderId);
//         if (!$order) {
//             throw new Exception('Order not found');
//         }

//         $order->status = $status;
//         $order->save();

//         DB::commit();
//         return response()->json([
//             'status' => 200,
//             'message' => 'Order status updated successfully!',
//         ]);
//     } catch (Exception $e) {
//         DB::rollback();
//         return response()->json([
//             'status' => 500,
//             'message' => $e->getMessage(),
//         ]);
//     }
// }

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

            $newPaymentStatus = $request->input('payment_status');

            if ($newPaymentStatus === 'paid' && $order->payment_status === 'unpaid') {
                $order->payment_status = 'paid';
                $order->save();
            }

            return response()->json(['message' => 'Order status updated successfully']);
        }

        // input kilo

        public function updateKilo(Request $request, Order $order)
        {
            $validatedData = $request->validate([
                'kilo' => 'required|numeric',
            ]);

            $category = $order->categories->first(); // Assuming you want to update the kilo value for the first category

            $order->categories()->updateExistingPivot($category->id, [
                'kilo' => $validatedData['kilo'],
            ]);

            return response()->json(['message' => 'Kilo value updated successfully']);
        }

        public function indexKilo()
        {
            $orders = Order::with('categories')->get(); // Retrieve all orders with their categories

            // return view('your_view_name', compact('orders'));
            return response()->json(['orders' => $orders]);
        }



 }