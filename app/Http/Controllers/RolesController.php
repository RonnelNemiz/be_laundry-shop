<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolesController extends Controller
{
    public function show()
    {
        $roles = Role::where('name', '!=', 'Customer')->get();

        return response()->json([
            'roles' => $roles,
        ]);
    }
}
