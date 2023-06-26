<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ItemCategory;
use App\Models\Handling;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function index()
    {
        // return Order::with('categories')->with('user.profile')->with('categories.parent')->get();
        $orders = Order::with(['profile', 'service', 'handling', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        return OrderResource::collection($orders);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $name = $request->name;
        $value = $request->value;

        if ($name === 'order') {
            $status = "";

            switch ($value) {
                case "Pending":
                    $status = 0;
                    break;
                case "Confirmed":
                    $status = 1;
                    break;
                case "On Queue":
                    $status = 2;
                    break;
                case "Washing":
                    $status = 3;
                    break;
                case "Ready for Payment":
                    $status = 4;
                    break;
                case "Completed":
                    $status = 5;
                    break;
                case "Canceled":
                    $status = 6;
                    break;
                default:
                    $status = 0;
                    break;
            }

            $order->update([
                'status' => $status,
            ]);
        } else if ($name === 'handling') {

            $status = "";

            switch ($value) {
                case "Ready on Pickup":
                    $status = 0;
                    break;
                case "Rider on Delivery":
                    $status = 1;
                    break;
                case "Ready fir Store Pickup":
                    $status = 2;
                    break;
                case "Delivered":
                    $status = 3;
                    break;
                case "Picked Up":
                    $status = 4;
                    break;
                default:
                    $status = 0;
                    break;
            }

            $order->update([
                'handling_status' => $status
            ]);
        } else {
            $payment = Payment::where('order_id', $order->id)->first();

            $status = "";

            switch ($value) {
                case "Unpaid":
                    $status = 0;
                    break;
                case "Paid":
                    $status = 1;
                    break;
                default:
                    $status = 0;
                    break;
            }

            $payment->update([
                'status' => $status
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => $name . " update successfully!"
        ]);
    }

    public function paying(Request $request, Order $order)
    {
        $paid = $order->update([
            'pay' => $request->pay,
            'change' => $request->change
        ]);

        $order->payment()->update([
            'status' => 1
        ]);

        if ($paid) {
            return response()->json([
                'status' => 200,
                'message' => "Your payment is successfully updated"
            ]);
        }
        return response()->json([
            'status' => 500,
            'message' => "Failed to submit payment!"
        ]);
    }

    public function saveOrderDetails(Request $request, Order $order)
    {
        $data = $request->all();

        try {
            foreach ($data as $categoryName => $categoryData) {
                $category = ItemCategory::where('name', $categoryName)->first();

                if (!isset($categoryData['children'])) {
                    continue; // Skip processing if 'children' key is not present
                }

                $kilo = $categoryData['kilo'];
                $children = $categoryData['children'];

                $hasNonEmptyChildQuantity = false;
                foreach ($children as $child) {
                    if (!empty($child['quantity'])) {
                        $hasNonEmptyChildQuantity = true;
                        break;
                    }
                }

                if (empty($kilo) && !$hasNonEmptyChildQuantity) {
                    continue; // Skip saving if both kilo and children quantities are empty
                }

                $breakdownHtml = $this->generateBreakdownHtml($children);
                $dividedKilo = ceil($kilo / 7);
                $totalPrice = $dividedKilo * $category->price;


                // Insert the parent category data
                DB::table('order_details')->upsert([
                    'order_id' => $order->id,
                    'service_id' => $category->service_id,
                    'category_id' => $category->id,
                    'weight' => $kilo,
                    'items_breakdown' => $breakdownHtml,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'total' => $totalPrice,
                ], ['order_id', 'category_id'], ['weight', 'items_breakdown', 'updated_at']);
            }

            return response()->json(['status' => 200, 'message' => 'Order details saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error saving order details', 'error' => $e->getMessage()], 500);
        }
    }


    public function updateOrderDetail(Request $request, $orderDetailsId)
    {
        $orderDetails = DB::table('order_details')->where('id', $orderDetailsId)->update([
            'items_breakdown' => $request->category
        ]);

        if ($orderDetails) {
            return response()->json(['status' => 200, 'message' => 'Order detail updated successfully']);
        }
        return response()->json(['status' => 500, 'message' => 'Failed to update order details']);
    }

    public function destroyOrderDetails($orderDetailsId)
    {
        $removed = DB::table('order_details')->where('id', $orderDetailsId)->delete();

        if ($removed) {
            return response()->json([
                'status' => 200,
                'message' => "Successfully remove order details"
            ]);
        }

        return response()->json([
            'status' => 404,
            'message' => "Order details not found!"
        ]);
    }

    public function destroyConsumable(Order $order, $consumableId)
    {
        $consumable = $order->consumables()->detach($consumableId);

        if ($consumable) {
            return response()->json([
                'status' => 200,
                'message' => "Successfully remove consumable"
            ]);
        }

        return response()->json([
            'status' => 404,
            'message' => "Consumable not found"
        ]);
    }

    private function generateBreakdownHtml($children)
    {
        $html = '';
        $hasNonEmptyValue = false;

        foreach ($children as $child) {
            $name = $child['name'];
            $quantity = $child['quantity'];
            if (!empty($quantity)) {
                $hasNonEmptyValue = true;
                $html .= '<li>' . $quantity . ' x ' . $name . '</li>';
            }
        }

        if ($hasNonEmptyValue) {
            $html = '<ul>' . $html . '</ul>';
        }

        return $html;
    }

    public function getPendingOrdersCount()
    {
        $pendingCount = Order::where('status', 'pending')->count();
        return $pendingCount;
    }

    public function totalneworders()
    {

        try {
            $today = Carbon::today();


            // Calculate today's orders
            $todaysOrdersCount = Order::whereDate('created_at', $today)->count();


            return response()->json([
                'status' => 200,
                'message' => "Order Successfully added!",
                'todays_orders_count' => $todaysOrdersCount,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to calculate sales.',
            ]);
        }
    }


    public function totalsales()
    {

        try {
            // Calculate total sales for today
            $today = Carbon::now()->format('Y-m-d');
            $totalTodaySales = Order::whereDate('created_at', $today)->sum('total');

            // Calculate total sales for the current week
            $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
            $totalWeekSales = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total');

            // Calculate total sales for the current month
            $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
            $totalMonthSales = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total');

            return response()->json([
                'status' => 200,
                'sales' => [
                    'today' => $totalTodaySales,
                    'week' => $totalWeekSales,
                    'month' => $totalMonthSales,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to calculate sales.',
            ]);
        }
    }

    public function customerHistory(Request $request)
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->with('categories')
            ->with('user.profile')
            ->with('categories.parent')
            ->with('payment')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'orders' => $orders
        ]);
    }



    public function orders(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 400,
                    'error' => 'User not found!'
                ]);
            }
            $profile = $user->profile;
            if (!$profile) {
                return response()->json([
                    'status' => 400,
                    'error' => 'Profile not found!'
                ]);
            }
            $handle = $request->handling;
            $serbesyo = $request->service;
            $personalInfo = $request->personal_details;
            $paymentMethod = $request->payment_method;

            $transNumber = Order::generateTransactionNumber();

            $newOrder = Order::create([
                'user_id' => $user->id,
                'profile_id' => $user->profile['id'],
                'handling_id' => $handle['id'],
                'service_id' => $serbesyo['id'],
                'trans_number' => $transNumber,
                'status' => 0,
                'handling_status' => 0
            ]);
            $newOrder->save();

            if ($newOrder) {
                $profile->update([
                    'first_name' => $personalInfo['first_name'],
                    'last_name' => $personalInfo['last_name'],
                    'land_mark' => $personalInfo['land_mark'],
                    'purok' => $personalInfo['purok'],
                    'brgy' => $personalInfo['brgy'],
                    'municipality' => $personalInfo['municipality'],
                    'contact_number' => $personalInfo['contact_number']
                ]);
                $profile->save();
                $payment = Payment::create([
                    'order_id' => $newOrder->id,
                    'payment_method_id' => $paymentMethod['id'],
                    'tendered' => 0,
                    'change' => 0,
                    'staff_id' => 0,
                    'status' => 0
                ]);
                $payment->save();
            } else {
                return response()->json([
                    'status' => 400,
                    'error' => $profile->error()
                ]);
            }

            DB::commit();

            $smsSetting = Setting::where('name', 'SMS')->first();

            if ($smsSetting->value === true) {
                $message = "Hi " . $profile->first_name . " " . $profile->last_name .
                    ', We have received your order. Your order reference number is '
                    . $transNumber . '. Thank you!';
                $this->deliverNotification($profile, $message);
            }
            return response()->json([
                'status' => 200,
                'message' => "Order Successfully added!"
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function adminAddOrders(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $profile = $user->profile;

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

            // $profile->update([
            //     'first_name' => $personalInfo['first_name'],
            //     'last_name' => $personalInfo['last_name'],
            //     'land_mark' => $personalInfo['land_mark'],
            //     'purok' => $personalInfo['purok'],
            //     'brgy' => $personalInfo['brgy'],
            //     'municipality' => $personalInfo['municipality'],
            //     'contact_number' => $personalInfo['contact_number']
            // ]);

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
                    $category = ItemCategory::where('name', $key)->first();
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


            $message = "Hi " . $profile->first_name . " " . $profile->last_name .
                ', We have received your order with reference number '
                . $transNumber . '. Thank you!';

            // $this->deliverNotification($profile, $message);
            return response()->json([
                'status' => 200,
                'message' => "Order Successfully added!"
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function view($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 500);
        }

        return response()->json($order, 200);
    }

    public function manualOrder(Request $request, Order $order)
    {

        DB::beginTransaction();
        try {
            // if (!$order) {
            //     throw new Exception('Order not found');
            // }

            $user = User::findOrFail($order->user_id);
            $profile = Profile::findOrFail($user->id);

            $user->update([
                'email' => $request->email,
            ]);
            $profile->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'purok' => $request->purok,
                'brgy' => $request->brgy,
                'municipality' => $request->municipality,
                'contact_number' => $request->contact_number
            ]);

            // Update the order fields based on the request data
            // $order->payment_status = $request->input('payment_status');
            // $order->status = $request->input('status');
            // $order->total = $request->input('total');
            // $order->ref_num = $request->input('ref_num');
            // $order->change = $request->input('change');
            // $order->amount = $request->input('amount');
            // $order->fabcon = $request->input('fabcon');
            // $order->detergent = $request->input('detergent');
            // $order->approved_by = $request->input('approved_by');

            // // Save the changes to the order
            // $order->save();

            // // Update the categories and their quantities if provided
            // $categories = $request->input('categories', []);
            // foreach ($categories as $categoryId => $quantity) {
            //     $category = Category::find($categoryId);
            //     if ($category) {
            //         // Update the quantity in the pivot table
            //         $order->categories()->updateExistingPivot($category->id, [
            //             'quantity' => $quantity,
            //         ]);
            //     }
            // }

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Order successfully updated!',
            ]);
            // return new OrderResource($order);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // public function updateStatus(Request $request, $id)
    // {
    //     $order = Order::findOrFail($id);

    //     $newStatus = $request->input('status');
    //     $customer = Profile::where('user_id', $order->user_id)->first();

    //     if ($newStatus === 'ready to pickup' && $order->status === 'pending') {
    //         $order->status = 'ready to pickup';
    //         $order->save();
    //     } elseif ($newStatus === 'in progress' && ($order->status === 'ready to pickup' || $order->status === 'pending')) {
    //         $order->status = 'in progress';
    //         $order->save();
    //     } elseif ($newStatus === 'ready for pickup' && $order->status === 'in progress') {
    //         $order->status = 'ready for pickup';
    //         $order->save();
    //     } elseif ($newStatus === 'ready to deliver' && ($order->status === 'ready for pickup' || $order->status === 'in progress')) {
    //         $order->status = 'ready to deliver';
    //         $order->save();
    //     } elseif ($newStatus === 'completed' && ($order->status === 'ready to deliver' || $order->status === 'ready for pickup')) {
    //         $order->status = 'completed';
    //         $order->save();
    //     }

    //     // send sms depends on the order status
    //     // if ($order->status === 'ready to pickup') {
    //     //     $message = "Good day " . $customer->first_name . ". Your dirty laundry is ready to pickup! Our team will collect it from your house. Please be ready. Thank you.";
    //     //     $this->deliverNotification($customer, $message);
    //     // } else if ($order->status === 'ready for pickup') {
    //     //     $message = "Good day " . $customer->first_name . ". Your laundry is ready for pickup! Collect your freshly laundered clothes at our laundry shop. The total amount to be paid is $" . $order->total . ".Thank you.";
    //     //     $this->deliverNotification($customer, $message);
    //     // } else if ($order->status === 'ready to deliver') {
    //     //     $message = "Good day " . $customer->first_name . ". Your freshly laundered clothes is ready for delivery! We'll bring it to your doorstep today. Thank you.";
    //     //     $this->deliverNotification($customer, $message);
    //     // }
    //     //  else if ($order->status === 'completed') {
    //     //     $message = "Your labhonon is completed and ready for pickup/delivery";
    //     //     $this->deliverNotification($customer, $message);
    //     // }

    //     return response()->json(['message' => 'Order status updated successfully']);
    // }


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
            // $order->approved_by = $user->first_name;
            // $order->save();
        }

        return response()->json(['message' => 'Order status updated successfully']);
    }

    // input kilo
    public function updateKilo(Request $request, Order $order, ItemCategory $category,)
    {
        $newKilo = $request->input('kilo');
        $data = [
            'kilo' => $newKilo
        ];

        //update the kilo in the piivo
        $order->categories()->updateExistingPivot($category, $data);

        $categories = ItemCategory::whereNull('parent_id')->get();
        $totalKilo = 0;
        $totalPrice = 0;

        foreach ($categories as $cat) {
            $kilo = $order->categories()->where('parent_id', $cat->id)->value('kilo');
            $price = $cat->price * $kilo / 7;

            $totalKilo += $kilo;
            $totalPrice += $price;
        }
        // Divide the total price by 7
        // $totalPrice = $totalPrice / 7;

        $handling = $order->handling;
        $service = $order->service;
        $addOns = $handling->handling_price + $service->service_price;

        $order->update([
            'total' => $totalPrice + $addOns,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully added kilo!'
        ]);

        // Update the kilo in the pivot table for each category
        // foreach ($order->categories as $category) {
        //     $order->categories()->updateExistingPivot($category->id, [
        //         'kilo' => $newKilo,
        //     ]);
        // }

        // return response()->json(['message' => 'Kilo updated successfully']);
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

        $amount = $validatedData['amount'];
        $change = $amount - $order->total;

        $order->update([
            'amount' => $amount,
            'change' => $change
        ]);

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


    public function updateDetergent(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->detergent_id = $request->input('detergent_id');
        $order->save();

        return response()->json(['message' => 'Detergent updated successfully']);
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

    public function orderDetails(Order $order)
    {
        // $order = DB::table('orders')
        //     ->join('profiles', 'orders.user_id', '=', 'profiles.user_id')
        //     ->where('orders.id', $id)
        //     ->select('orders.*', 'profiles.*')
        //     ->first();

        $orderItems = DB::table('order_details')
            ->join('item_categories', 'order_details.category_id', '=', 'item_categories.id')
            ->select('item_categories.*', 'order_details.*')
            ->where('order_id', $order->id)->get();

        $categoryParent = DB::table('item_categories')->get();
        $categoryChildren = DB::table('item_types')
            ->select('category_id', DB::raw('JSON_ARRAYAGG(name) as children'))
            ->groupBy('category_id')
            ->get();

        $categories = [];

        foreach ($categoryParent as $parent) {
            $parentCategory = [
                'id' => $parent->id,
                'name' => $parent->name,
                'children' => [],
            ];

            foreach ($categoryChildren as $child) {
                if ($child->category_id === $parent->id) {
                    $childCategory = [
                        'category_id' => $child->category_id,
                        'name' => json_decode($child->children),
                    ];

                    $parentCategory['children'][] = $childCategory;
                }
            }

            $categories[] = $parentCategory;
        }

        return response()->json([
            'order' => $order,
            'orderItems' => $orderItems,
            'categories' => $categories,
            'profile' => $order->profile,
            'consumables' => $order->consumables
        ]);
    }

    public function updateOrderDetails(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            $profile = Profile::where('user_id', $order->user->id)->first();

            if (empty($profile)) {
                throw new Exception('No Profile Found');
            }

            $profile = tap($profile)->update($request->all());

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateOrderItems(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            foreach ($request->all() as $key => $value) {
                if ($value['id']) {
                    $categoryUser = DB::table('category_user')->where('order_id', $order->id)->where('category_id', $value['id']);
                    if ($categoryUser) {
                        // $categoryUser->quantity = $value->
                    }
                }
            }
        } catch (Exception $e) {
        }
    }
}
