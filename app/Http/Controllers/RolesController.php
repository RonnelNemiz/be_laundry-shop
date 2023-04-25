<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolesController extends Controller
{
    public function show()
    {

        return response()->json([
            'roles' => Role::all(),
        ]);
    }
}
