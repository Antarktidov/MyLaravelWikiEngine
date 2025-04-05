<?php

namespace App\Http\Middleware;

use App\Helpers\PermissionChecker;

use Illuminate\Http\Response;
use Illuminate\Database\Query\Builder;
use Closure;
use Illuminate\Http\Request;
//use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Wiki;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class ManageLocalUserrightsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
     public function handle(Request $request, Closure $next): Response
     {
         return PermissionChecker::check_for_middleware($request, $next, 'can_manage_local_userrights');
     }

    /*private function getUserGroupWiki($user, $wiki)
    {
        return DB::table('user_user_group_wiki')
            ->where('user_id', $user->id)
            ->where(function (Builder $query) use ($wiki) {
                $query->where('wiki_id', 0)
                    ->orWhere('wiki_id', $wiki->id);
            })
            ->get();
    }*/
}
