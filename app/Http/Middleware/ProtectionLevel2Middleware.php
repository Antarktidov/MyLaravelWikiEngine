<?php

namespace App\Http\Middleware;

use App\Helpers\PermissionChecker;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Option;

class ProtectionLevel2Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $options = Option::getOptions();

        if ($options->protection_level === 'semi_public') {
            return PermissionChecker::check_for_middleware($request, $next, 'can_edit_articles');
        }
        return $next($request);
    }
}
