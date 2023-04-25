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
            'land_mark' => $request->land_mark,
            'municipality' => $request->municipality,
            'contact_number' => $request->contact_number,
        ]);

        $user = User::find($profile->user_id);

        $user->update([
            'email' => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Sucessfully Updated!"
        ]);
    }

    public function destroy($id)
    {
        $profile = Profile::find($id);
        $profile->delete();
        return response()->json([200, "Successfully Deleted!" ]);
    }
}
