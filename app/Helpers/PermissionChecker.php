<?php
namespace App\Helpers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

use App\Models\User;
use App\Models\Wiki;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class PermissionChecker
{
    public static function check($user, $wikiName, $permission)
    {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $user_user_group_wiki = self::getUserGroupWiki($user, $wiki);
        } else {
            $user_user_group_wiki = self::getUserGroup($user);
        }
        $user_groups = UserGroup::all();

        foreach ($user_user_group_wiki as $user_user_group_wiki_foreach) {
            foreach ($user_groups as $user_group) {
                if ($user_user_group_wiki_foreach->user_group_id === $user_group->id) {
                    if ($user_group->$permission === 1) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function check_for_middleware($request, $next, $permission): Response
{
    $user = auth()->user();

    if ($user) {
        $wikiName = $request->route('wikiName');
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $user_user_group_wiki = self::getUserGroupWiki($user, $wiki);
            //dd('Группы юзера получены ВМЕСТЕ вики');
        } else {
            $user_user_group_wiki = self::getUserGroup($user);
            //dd('Группы юзера получены БЕЗ вики');
        }
            //dd('До итерации');
            $user_groups = UserGroup::all();
            foreach ($user_user_group_wiki as $user_user_group_wiki_foreach) {
                foreach ($user_groups as $user_group) {
                    //dd('foreach ($user_groups as $user_group итерация 1');
                    if ($user_user_group_wiki_foreach->user_group_id === $user_group->id) {
                        if ($user_group->$permission === 1) {
                            return $next($request);
                        }
                    }
                }
            //}

            return response('Forbidden', 403);
        } /*else {
            return response('Wiki not found', 404);
        }*/
    } else {
        return response('Unauthorized', 401);
    }
    return response('Forbidden', 403);
}

    private static function getUserGroupWiki($user, $wiki)
    {
        return DB::table('user_user_group_wiki')
            ->where('user_id', $user->id)
            ->where(function (Builder $query) use ($wiki) {
                $query->where('wiki_id', $wiki->id)
                    ->orWhere('wiki_id', 0);
            })
            ->get();
    }

    private static function getUserGroup($user)
    {
        return DB::table('user_user_group_wiki')
            ->where('user_id', $user->id)
            ->where('wiki_id', 0)
            ->get();
    }
}
