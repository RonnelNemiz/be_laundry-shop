<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payment = Payment::all();
        return $payment;
    }
   
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'payment_name' => 'required|max:50',
        ]);

        $payment = Payment::create($validatedData);

        return response()->json([
            $payment,
            'status' => 200,
        ]);
    }

    public function show()
    {
        $payments = Payment::get();
    
        return response()->json($payments);
    }
    public function view($id)
    {
       $payment = Payment::find($id);

       if(!$payment){
            return response()->json([
                'message' => 'Payment Method Not Found',
            ], 500);
       }
        return response()->json($payment, 200);
    }
    

    public function update(Request $request, Payment $payment)
    {
        $validatedData = $request->validate([
            'payment_name' => 'required|string|max:50',
        ]);
    
        $payment->update($validatedData);
    
        return response()->json([
            'message' => 'Payment data updated successfully',
            'data' => $payment
        ], 200);
    }
  
    
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json([200, "Successfully Deleted!"]);
    }

}
