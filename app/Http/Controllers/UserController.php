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

        $query = Profile::query();
        if (!is_null($search)) {
            $query->where('first_name', 'LIKE', "%$search%")->orWhere('last_name', 'LIKE', "%$search%");
        }

        return ProfileResource::collection($this->paginated($query, $request));
    }

     // $customers = User::whereHas('roles', function ($query) {
        //     $query->where('name', 'Customer');
        // })->get();

        // return view('users.index', compact('customers'));

    public function getCustomers(Request $request)
    {
       
        
            $search = $request->search;
    
            $query = Profile::query()
                ->whereHas('user', function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'Customer');
                    });
                });
    
            if (!is_null($search)) {
                $query->where('first_name', 'LIKE', "%$search%")->orWhere('last_name', 'LIKE', "%$search%");
            }
    
            return ProfileResource::collection($this->paginated($query, $request));
            // $search = $request->search;

            // $query = User::query()
            //     ->whereHas('roles', function ($query) {
            //         $query->where('name', 'Customer');
            //     });
        
            // if (!is_null($search)) {
            //     $query->where(function ($query) use ($search) {
            //         $query->where('first_name', 'LIKE', "%$search%")
            //               ->orWhere('last_name', 'LIKE', "%$search%");
            //     });
            // }
        
            // return ProfileResource::collection($this->paginated($query, $request));
    
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
            'password'  => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $profile = Profile::find($user->profile_id);

        $profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'purok' => $request->purok,
            'brgy' => $request->brgy,
            'land_mark' => 'leyte',
            'municipality' => $request->municipality,
            'contact_number' => $request->contact_number,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Sucessfully Updated!"
        ]);
    }

   

    public function destroy(User $user)
    {
        $profile = Profile::where('user_id', $user->id)->first();

        if ($profile) {
            $profile->delete();
        }

        $user->delete();

        return response()->json([200, "Successfully Deleted!"]);
    }
}
