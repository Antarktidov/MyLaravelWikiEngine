<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;

use Illuminate\Http\Request;

class PermissionsManagerController extends Controller
{
    public function index() {
        $user_groups = UserGroup::all();
        //dd($user_groups);
        return view('permissions_manager', compact('user_groups'));
    }
}
