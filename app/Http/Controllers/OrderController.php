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

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();
        return $query->has('categories')->with('categories.category.parent')->get();

        return OrderResource::collection($this->paginated($query, $request));
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
}
