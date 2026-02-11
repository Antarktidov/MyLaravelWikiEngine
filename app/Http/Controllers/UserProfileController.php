<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserProfileRevision;
use App\Models\User;
use App\Models\UserUserGroupWiki;
use App\Models\UserGroup;
use App\Models\Wiki;

class UserProfileController extends Controller
{
    public function show_global(User $user) {
        /*$user_profile = UserProfileRevision::where('user_id', $user->id)
        ->where('wiki_id', 0)
        ->where('is_approved', true)
        ->whereNull('deleted_at')
        ->orderBy('id', 'desc')
        ->first();*/

        $user_usergroups_wiki = UserUserGroupWiki::where('user_id', $user->id)
        ->where('wiki_id', 0)
        ->select('user_group_id')
        ->get();

        $user_group_names = [];

        foreach($user_usergroups_wiki as $item) {
            $user_group_names[] = UserGroup::find($item->user_group_id)->name;
        }

        $wiki = Wiki::withTrashed()->first();
        $user2 = auth()->user();
        if ($user2 != null) {
            $can_review_user_profiles = $user->can('review_user_profiles', $wiki->url);
            $is_my_profile = $user2->id === $user->id;
        } else {
            $can_review_user_profiles = false;
            $is_my_profile = false;
        }

        if ($can_review_user_profiles || $is_my_profile) {
            $user_profile = UserProfileRevision::where('user_id', $user->id)
            ->where('wiki_id', 0)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')->first();
        } else {
            $user_profile= UserProfileRevision::where('user_id', $user->id)
            ->where('wiki_id', 0)
            ->where('is_approved', true)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')->first();
        }

        return view('userprofile-global', compact('user_profile', 'user',
                                        'user_group_names', 'can_review_user_profiles',
                                        'is_my_profile'));
    }
}
