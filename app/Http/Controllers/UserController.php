<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ProfileResource;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $search = $request->search;

        $query = User::query();
        $query->where("role", '!=', "Customer");
        $query->where('role', '!=', 'Administrator');
        $query->orderBy('id', 'desc');
        return UserResource::collection($this->paginated($query, $request));
    }

    public function store(Request $request)
    {
        $user = User::where('email', $request->email)->first();


        if (empty($user)) {
            $newUser = User::create([
                "email" => $request->email,
                "password" => $request->password,
                "role" => $request->role,
            ]);

            Profile::create([
                'user_id' => $newUser->id,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "purok" => $request->purok,
                "brgy" => $request->brgy,
                "municipality" => $request->municipality,
                "land_mark" => "Leyte",
                "contact_number" => $request->contact_number,
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Sucessfully Added!"
            ]);
        };

        return response()->json([
            'status' => 500,
            'message' => "email is taken!"
        ]);
    }

    public function editUser(User $user, Request $request)
    {
        $user->update([
            'email' => $request->email,
            // 'password'  => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $profile = $user->profile;

        $profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'purok' => $request->purok,
            'brgy' => $request->brgy,
            'land_mark' => $request->land_mark,
            'municipality' => $request->municipality,
            'contact_number' => $request->contact_number,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Sucessfully Updated!"
        ]);
    }

    // Customers

    public function getCustomers(Request $request)
    {


        $search = $request->search;
        // $query = Profile::query()
        //     ->whereHas('user', function ($query) {
        //         $query->whereHas('roles', function ($query) {
        //             $query->where('name', 'Customer');
        //         });
        //     });

        // if (!is_null($search)) {
        //     $query->where('first_name', 'LIKE', "%$search%")->orWhere('last_name', 'LIKE', "%$search%");
        // }

        // return ProfileResource::collection($this->paginated($query, $request));
        $query = User::query();
        $query->where("role", "Customer");
        $query->orderBy('id', 'desc');
        return UserResource::collection($this->paginated($query, $request));
      
    }
    public function addCustomer(Request $request)
    {
        $user = User::where('email', $request->email)->first();


        if (empty($user)) {
            $newUser = User::create([
                "email" => $request->email,
                "password" => $request->password,
                "role" => "Customer",
            ]);

            Profile::create([
                'user_id' => $newUser->id,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "purok" => $request->purok,
                "brgy" => $request->brgy,
                "municipality" => $request->municipality,
                "land_mark" =>  $request->land_mark ,
                "contact_number" => $request->contact_number,
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Sucessfully Added!"
            ]);
        };

        return response()->json([
            'status' => 500,
            'message' => "email is taken!"
        ]);
    }

    public function destroy(User $user)
    {
        $user->profile->delete();
        $user->delete();
        return response()->json([200, "Successfully Deleted!"]);
    }
   

}
