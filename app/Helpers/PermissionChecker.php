<?php
namespace App\Helpers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

use App\Models\User;
use App\Models\Wiki;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

/**
 * Класс PermissionChecker - это хелпер (помощник), который позволяет нам проверить,
 * имеет ли пользователь какое-либо техническое право на данной вики или глобально
*/
class PermissionChecker
{
    /**
     * Данная функция предназначена для вызова в файле AppServiceProvider.php
     * Принимаемые параметры: пользователь, имя-url вики, техническое право
     */
    public static function check($user, $wikiName, $permission)
    {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        //ищем вики по url
        if ($wiki) {
            //получаем права на вики
            $user_user_group_wiki = self::getUserGroupWiki($user, $wiki);
            //dd($user_user_group_wiki);
        } else {
            //получаем права вне вики (для управления вики-фермой)
            $user_user_group_wiki = self::getUserGroup($user);
        }

        $user_groups = UserGroup::all();//получаем все группы
        //dd($user_groups);

        /**
         * Ищем, где существующая группа (на вики или глобально) совпадает с
         * группой участника.
         */
        foreach ($user_user_group_wiki as $user_user_group_wiki_foreach) {
            foreach ($user_groups as $user_group) {
                if ($user_user_group_wiki_foreach->user_group_id === $user_group->id) {
                    /*dump($permission);
                    dump($user_group->can_check_revisions);
                    dump($user_group->$permission);*/
                    if ($user_group->$permission === 1) {
                        //exit(-1);
                        return true;
                        /**
                         * Если у группы, которой владеет участник,
                         * есть техническое право, то участник имеет техническое право...
                         */
                    }
                }
            }
        }
        //exit(-1);
        return false;//... в противном случае он его не имеет
    }

    /**
     * Данная функция предназвачена для вызова в Middleware.
     */
    public static function check_for_middleware($request, $next, $permission)
    {
        $user = auth()->user();

        if ($user) {
            $wikiName = $request->route('wikiName');//получаем url вики
            $wiki = DB::table('wikis')->where('url', $wikiName)->first(); //получем вики по url
            if ($wiki) {
                //получаем права на вики
                $user_user_group_wiki = self::getUserGroupWiki($user, $wiki);
            } else {
                //получаем права на вики-ферме
                $user_user_group_wiki = self::getUserGroup($user);
            }
                $user_groups = UserGroup::all();//получаем все группы
                //dd($user_groups);

                /**
                 * Ищем, где сущесвующая группа (на вики или глобально) совпадает с
                 * группой участника.
                 */
                foreach ($user_user_group_wiki as $user_user_group_wiki_foreach) {
                    foreach ($user_groups as $user_group) {

                        if ($user_user_group_wiki_foreach->user_group_id === $user_group->id) {
                            /*dump($permission);
                            dump($user_group->can_delete_revisions);
                            dump($user_group->$permission);*/
                            if ($user_group->$permission === 1) {
                                //exit(-1);
                                return $next($request);
                                /**
                                * Если у группы, которой владеет участник,
                                * есть техническое право, то участник имеет техническое право.
                                */
                            }
                        }
                    }
                    //exit(-1);
                    /*return response('Forbidden', 403)
                        ->header('Content-Type', 'text/plain');*/
            }
        } else {
            return response('Unauthorized', 401)
                ->header('Content-Type', 'text/plain');
        }
        return response('Forbidden', 403)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Приватная функция для получения прав участника на конкретной вики
     * или глобальный прав. wiki_id 0 - магичсекая константа для глобальных групп
     * (групп, не привязанных к вики)
     */
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
    /**
     * Приватная функция для получения прав участников вне вики. Рабоатет только
     * для глобальных групп
     */

    private static function getUserGroup($user)
    {
        return DB::table('user_user_group_wiki')
            ->where('user_id', $user->id)
            ->where('wiki_id', 0)
            ->get();
    }
}
