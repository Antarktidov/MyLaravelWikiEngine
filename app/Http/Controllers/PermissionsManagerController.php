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

    public function store(Request $request) {
        $data = request()->validate([
            'user-group-names' => 'array',
            'user-group-is-global' => 'array',
            'user-group-permissions' => 'array',
        ]);
        dd($data);
        return 'Заглушка для стора разрешений';
    }
}
