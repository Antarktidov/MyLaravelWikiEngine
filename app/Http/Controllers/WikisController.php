<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wiki;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;
use Illuminate\Support\Facades\DB;

class WikisController extends Controller
{
    public function index() {
        $wikis = Wiki::all();
        
        return view('show-all-wikis', compact('wikis'));
    }

    public function show_closed_wikis() {
        $wikis = Wiki::onlyTrashed()->get();

        //dd($wikis);
        
        return view('show-all-closed-wikis', compact('wikis'));
    }

    public function create() {
        return view('create-wiki');
    }

    public function store() {
        $data = request()->validate([
            'url' => 'string',
        ]);
        Wiki::create($data);

        return 'Вики успешно создана!';
    }

    public function destroy(Wiki $wiki) {
        //$wiki = Wiki::find($wikiId);
        //dd($wiki);
        $wiki->delete();
        return 'Вики успешно закрыта!';
    }

    public function open($wikiId) {
        //dd($wikiId);
        $wiki = Wiki::onlyTrashed()->findOrFail($wikiId);
        //dd($wiki);
        $wiki->restore();
        return 'Вики успешно открыта!';
    }

    public function manage_global_user_rights($userId) {
        $managed_user = User::findOrFail($userId);
        $user_groups = UserGroup::all();
        $user_user_group_wiki = UserUserGroupWiki::all();

        return view('manage_user_groups', compact('managed_user', 'user_groups', 'user_user_group_wiki'));
    }
    public function store_global_user_rights($userId) {
        $data = request()->validate([
            'user_group_ids' => 'array',
        ]);
        $managed_user = User::findOrFail($userId);
        $user_user_group_wiki = DB::table('user_user_group_wiki')
        ->where('user_id', $userId)
        ->where('wiki_id', 0)
        //->where('user_group_id', $data['user_group_ids'])
        ->get();
        $user_user_group_wiki_ids = [];

        for ($i = 0; $i < count($user_user_group_wiki); $i++) {
            array_push($user_user_group_wiki_ids, $user_user_group_wiki[$i]->user_group_id);
        }

        if(isset($data['user_group_ids'])) {
            $user_group_ids = $data['user_group_ids'];
        }
        else {
            $user_group_ids = [];
        }

        for ($i = 0; $i < count($user_group_ids); $i++) {
            $user_group_ids[$i] = (int)$user_group_ids[$i];
        }

        $user_group_ids_to_remove = array_diff($user_user_group_wiki_ids, $user_group_ids);
        $user_group_ids_to_add = array_diff($user_group_ids, $user_user_group_wiki_ids);

        /*$user_groups_to_remove = */DB::table('user_user_group_wiki')
        ->where('user_id', $userId)
        ->where('wiki_id', 0)
        ->where('user_group_id', $user_group_ids_to_remove)
        ->each()
        ->delete();
    }
}
