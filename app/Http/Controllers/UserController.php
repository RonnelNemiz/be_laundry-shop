<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->search;

        $query = User::query();
        $query->where("role", '!=', "Customer");
        $query->where('role', '!=', 'Administrator');
        $query->orderBy('id', 'desc');
        return UserResource::collection($this->paginated($query, $request));
    }
    public function customer()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Customer not found!'
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Customer found!',
                'user' => new UserResource($user)
            ]);
        }
    }

    public function customerUpdateProfile(User $user, Request $request)
    {
        $user->update([
            'email' => $request->email,
            'password'  => Hash::make($request->password),
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
            'message' => "Sucessfully Updated!",
            'user' => new UserResource($user)
        ]);
    }

    public function getAllUsers(Request $request)
    {
        $query = User::query();
        $query->where('role', '!=', 'Administrator');
        $query->orderBy('id', 'desc');
        return UserResource::collection($this->paginated($query, $request));
    }


    public function store(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $image = $request->file('image');
        if (empty($user)) {
            $newUser = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "role" => $request->role,
            ]);

            if ($image) {
                $filename = "image" . "_" . Str::random(5) . "." . $image->getClientOriginalExtension();
                if (!Storage::disk('public')->exists('images')) {
                    Storage::disk('public')->makeDirectory('images');
                }
                $image->storeAs('/images', $filename);
                $imageUrl = asset('storage/images/' . $filename);

                $profile = Profile::create([
                    'user_id' => $newUser->id,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "purok" => $request->purok,
                    "brgy" => $request->brgy,
                    "municipality" => $request->municipality,
                    "land_mark" => "Leyte",
                    "contact_number" => $request->contact_number,
                    "image" => $imageUrl,
                ]);
            } else {
                $profile = Profile::create([
                    'user_id' => $newUser->id,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "purok" => $request->purok,
                    "brgy" => $request->brgy,
                    "municipality" => $request->municipality,
                    "land_mark" => "Leyte",
                    "contact_number" => $request->contact_number,
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => "Successfully Added!",
            ]);
        }

        return response()->json([
            'status' => 500,
            'message' => "Email is taken!",
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
                "land_mark" =>  $request->land_mark,
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

    public function showCustomer($id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'message' => 'Not Found',
            ], 500);
        }

        $profile = $user->profile;

        // Append the image URL to the profile object
        $profile->image_url = Storage::url($profile->image);

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->profile->delete();
        $user->delete();
        return response()->json([200, "Successfully Deleted!"]);
    }
}
