<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'purok' => 'required',
            'brgy' => 'required',
            'municipality' => 'required',
            'contact_number' => 'required',
            'land_mark' => 'required',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if($validated){
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);
            $profile = new Profile();
            $profile->user_id = $user['id'];
            $profile->purok = $request->purok;
            $profile->brgy = $request->brgy;
            $profile->land_mark = $request->land_mark;
            $profile->municipality = $request->municipality;
            $profile->contact_number = $request->contact_number;
            $profile->save();

            return response()->json([
                'code' => 200,
                'message' => 'success'
            ]);
        }
        else{
            abort(402,'Failed to create');
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

    public function getCustomers(){
        $customers = User::with('profile')->where('role','Customer')->get();
        return $customers;
    }
}
