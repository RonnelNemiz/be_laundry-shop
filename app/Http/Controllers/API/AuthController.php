<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken($user->email . '_Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'username' => $user->name,
                'token' => $token,
                'message' => 'Registration Successfull',
            ], 200);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|max:191',
            'password' => 'required',
        ]);

        if (auth()->attempt($request->only(['email', 'password']))) {
            $user = auth()->user();
            $data = $user->createToken(env('APP_URL'))->accessToken;

            return response()->json([
                'code' => 200,
                'access_token' => $data,
                'user' => $user
            ]);
        } else {
            abort(422, 'Invalid Credentials');
        }
    }
}