<?php

namespace Bo\PermissionManager\App\Traits;

use Route;
use Exception;

trait CheckPermission
{
    /**
     * Check route name alias (route admin must have alias name)
     *
     * @param $request
     *
     * @return void
     * @throws Exception
     */
    public function checkRouteName($request)
    {
        //Permission on admin
        $route = Route::getRoutes()->match($request);
        if ($route->getPrefix() == config('bo.base.route_prefix', 'admin')) {
            $array_middleware = $route->gatherMiddleware();

            $flag_check_middleware = false;
            foreach ($array_middleware as $middleware) {
                if (is_string($middleware) && ($middleware == 'web' || $middleware == 'api')) $flag_check_middleware = true;
            }

            if (!$flag_check_middleware) {
                throw new Exception("Route must have middleware web or api . Please add middleware web or api to route !");
            }

            if (is_null($route->getName()) || mb_strlen($route->getName()) < 1) {
                throw new Exception("Route must have alias name route [ Example : Route::get('/test',fn() => 'Route Test')->name('route.test'); ] !");
            }
        }
    }

    /**
     * Check permission by route name alias
     *
     * @param $request
     *
     * @return void
     * @throws Exception
     */
    public function checkPermission($request)
    {
        $route = $request->route();
        if ($route->getPrefix() == config('bo.base.route_prefix', 'admin')) {
            if (!$this->passPermissionRoute($route->getName())) {
                $users = bo_user();
                if ($users && !$users->hasPermissionTo($route->getName()) && env('PERMISSION_ADMIN')) {
                    abort(500, 'No permission');
                }
            }
        }
    }

    /**
     * Check permission user
     *
     * @param string $route_name
     * @return bool
     * */
    public function hasPermissionTo(string $route_name): bool
    {
        $roles = $this->roles()->pluck('list_route_admin', 'id')->toArray();
        $roles = \Arr::collapse($roles);
        $role_unique = array_unique($roles, SORT_REGULAR);

        $array_role = [];
        foreach ($role_unique as $role) {
            if (isset($role['route_name'])) {
                $array_role[] = $role['route_name'];
            }
        }

        return in_array($route_name, $array_role);
    }

    /**
     * Passmermission route
     *
     * @param string $route_name
     * @return bool
     * */
    public function passPermissionRoute(string $route_name): bool
    {
        $array_ignore_route_permission = config('bo.permissionmanager.ignore_route_permission', []);

        return (in_array($route_name, $array_ignore_route_permission) || $this->checkEndsWith($route_name));
    }

    /**
     * Check end with string route
     *
     * @param string $route_name
     * @return bool
     * */
    private function checkEndsWith(string $route_name): bool
    {
        $array_ignore_by_regex = config('bo.permissionmanager.ignore_route_permission_by_regex', []);
        foreach ($array_ignore_by_regex as $value) {
            if (str_ends_with($route_name, $value)) return true;
        }
        return false;
    }
}
