<?php
namespace App\Http\Middleware;

use App\Support\MkwepAccess;
use Closure;
use Illuminate\Http\Request;

class MkwepRouteAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('mkwep_access.enabled', false)) return $next($request);
        $route = $request->route();
        $name = $route?->getName();
        $module = MkwepAccess::moduleForRoute($name);
        if (!$module) return $next($request);
        if (!$request->user()) abort(403);
        $action = MkwepAccess::actionForRoute($name, $route->methods());
        abort_unless(MkwepAccess::allowed($request->user(), $module, $action), 403);
        return $next($request);
    }
}
