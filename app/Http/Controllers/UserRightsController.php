<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;
use App\Models\Wiki;
class UserRightsController extends Controller
{
    //TODO: разделить логику управления локальными и глоабьными группами, избавиться от дублирующегося кода

    //Форма для управления глобальными группами
    public function manage_global_user_rights(int $userId) {
        $managed_user = User::findOrFail($userId);
        $user_groups = UserGroup::where('is_global', 1)->get();
        $user_user_group_wiki = UserUserGroupWiki::where('wiki_id', 0)->get();

        return view('manage_user_groups', compact('managed_user', 'user_groups', 'user_user_group_wiki'));
    }
    //POST-ручка для сохранения глобальных прав участника
    //Уровень доступа - глобальная группа steward
    public function store_global_user_rights(int $userId) {
        $data = request()->validate([
            'user_group_ids' => 'array',
        ]);

        $managed_user = User::findOrFail($userId);//ищем управляемого участника
        $user_user_group_wiki = UserUserGroupWiki::where('user_id', $userId)
            ->where('wiki_id', 0)
            ->get();
        //получаем текущие группы
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

        $user_group_ids_to_remove = array_values($user_group_ids_to_remove);
        $user_group_ids_to_add = array_values($user_group_ids_to_add);

        for ($i = 0; $i < count($user_group_ids_to_remove); $i++) {
            UserUserGroupWiki::where('user_id', $userId)
                ->where('wiki_id', 0)
                ->where('user_group_id', $user_group_ids_to_remove[$i])
                ->delete();
        }
        for ($i = 0; $i < count($user_group_ids_to_add); $i++) {
            $one_user_group_to_add = [
                'user_id' => $userId,
                'user_group_id' => $user_group_ids_to_add[$i],
                'wiki_id' => 0,
            ];
            UserUserGroupWiki::create($one_user_group_to_add);
        }

        return response(__('The user\'s usergroups was changed'), 200)
            ->header('Content-Type', 'text/plain');
    }


    /**
     * Управление локальными групами. Уровень дсотупа - steward и admin
     */
    public function manage_local_user_rights(string $wikiName, int $userId) {
        $wiki = Wiki::where('url', $wikiName)->first();
        if ($wiki) {
            $managed_user = User::findOrFail($userId);
            $user_groups = UserGroup::where('is_global', 0)->get();
            $user_user_group_wiki = UserUserGroupWiki::where('wiki_id', $wiki->id)->get();

            return view('manage_local_user_groups', compact('wiki', 'managed_user', 'user_groups', 'user_user_group_wiki'));
        }
    }
    /**
     * POST-ручка для управления локальными группами
     */
    public function store_local_user_rights(string $wikiName, int $userId) {
        $wiki = Wiki::where('url', $wikiName)->first();
        if ($wiki) {
            $data = request()->validate([
                'user_group_ids' => 'array',
            ]);
            $managed_user = User::findOrFail($userId);
            $user_user_group_wiki = UserUserGroupWiki::where('user_id', $userId)
                ->where('wiki_id', $wiki->id)
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

            $user_group_ids_to_remove = array_values($user_group_ids_to_remove);
            $user_group_ids_to_add = array_values($user_group_ids_to_add);

            for ($i = 0; $i < count($user_group_ids_to_remove); $i++) {
                UserUserGroupWiki::where('user_id', $userId)
                    ->where('wiki_id', $wiki->id)
                    ->where('user_group_id', $user_group_ids_to_remove[$i])
                    ->delete();
            }
            for ($i = 0; $i < count($user_group_ids_to_add); $i++) {
                $one_user_group_to_add = [
                    'user_id' => $userId,
                    'user_group_id' => $user_group_ids_to_add[$i],
                    'wiki_id' => $wiki->id,
                ];
                UserUserGroupWiki::create($one_user_group_to_add);
            }

            return response(__('The user\'s usergroups was changed'), 200)
                ->header('Content-Type', 'text/plain');
        }
    }
}
