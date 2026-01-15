<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserProfileRevision;
use App\Models\User;
use App\Models\UserUserGroupWiki;
use App\Models\UserGroup;

class UserProfileController extends Controller
{
    public function show_global(User $user) {
        $user_profile = UserProfileRevision::where('user_id', $user->id)
        ->where('wiki_id', 0)
        ->where('is_approved', true)
        ->whereNull('deleted_at')
        ->orderBy('id', 'desc')
        ->first();

        $user_usergroups_wiki = UserUserGroupWiki::where('user_id', $user->id)
        ->where('wiki_id', 0)
        ->select('user_group_id')
        ->get();

        //dd($user_usergroups_wiki);

        $user_group_names = [];

        foreach($user_usergroups_wiki as $item) {
            $user_group_names[] = UserGroup::find($item->user_group_id)->name;
        }

        return view('userprofile-global', compact('user_profile', 'user', 'user_group_names'));
    }
}
