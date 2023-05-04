<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ProfileResource;

class ProfileController extends Controller
{
    public function editProfile(Profile $profile, Request $request)
    {
        $profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'purok' => $request->purok,
            'brgy' => $request->brgy,
            'land_mark' => 'leyte',
            'municipality' => $request->municipality,
            'contact_number' => $request->contact_number,
        ]);
        $user = $profile->user;

        $user->update([
            'email' => $request->email,
            'role' => 'Customer',
        ]);
    
            return response()->json([
                'status' => 200,
                'message' => "Sucessfully Updated!"
            ]);
        }
}
