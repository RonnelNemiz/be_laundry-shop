<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethod = PaymentMethod::all();
        return $paymentMethod;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:50',
            'recipient' => 'required|max:50',
            'number' => 'required|numeric',
            'special_instructions' => 'required',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Get the uploaded image file
        $imageFile = $request->file('logo');

        // Generate a unique file name
        $fileName = uniqid('logo_') . '.' . $imageFile->getClientOriginalExtension();

        // Define the path to store the original image
        $imagePath = 'public/images/' . $fileName;

        // Save the original image
        Storage::putFileAs('public/images', $imageFile, $fileName);

        // Get the public URL of the original image
        $imageUrl = Storage::url($imagePath);

        $validatedData['logo'] = $imageUrl;

        $paymentMethod = PaymentMethod::create($validatedData);

        return response()->json([
            $paymentMethod,
            'status' => 200,
        ]);
    }

    public function show()
    {
        $paymentMethod = PaymentMethod::get();

        return response()->json($paymentMethod);
    }
    public function view($id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response()->json([
                'message' => 'Payment Method Not Found',
            ], 500);
        }
        return response()->json($paymentMethod, 200);
    }


    public function update(Request $request, PaymentMethod $paymentMethod)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'logo' => '',
            'recipient' => 'required|max:255',
            'number' => 'required|numeric',
            'special_instructions' => 'required',
        ]);

        $paymentMethod->update($validatedData);

        return response()->json([
            'message' => 'Payment Method data updated successfully',
            'data' => $paymentMethod
        ], 200);
    }


    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return response()->json([200, "Successfully Deleted!"]);
    }
}
