<?php

namespace App\Http\Middleware;

use Closure;
use Bo\PermissionManager\App\Traits\CheckPermission as TraitCheckPermission;
use Illuminate\Http\Request;

class CheckPermission
{
    use TraitCheckPermission;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->checkPermission($request);
        return $next($request);
    }
}
