<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Bo\PermissionManager\App\Traits\CheckPermission;

class CheckRouteName
{
    use CheckPermission;
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $this->checkRouteName($request);
        return $next($request);
    }
}
