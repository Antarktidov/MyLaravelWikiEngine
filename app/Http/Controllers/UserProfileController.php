<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserProfileRevision;
use App\Models\User;

class UserProfileController extends Controller
{
    public function show_global(User $user) {
        $user_profile = UserProfileRevision::where('user_id', $user->id)
        ->where('wiki_id', 0)
        ->where('is_approved', true)
        ->whereNull('deleted_at')
        ->orderBy('id', 'desc')
        ->first();

        return view('userprofile-global', compact('user_profile', 'user'));
    }
}
