<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used in the User, Role and Permission CRUDs.
    |
    */

    'models'                           => [
        'user' => config('bo.base.user_model_fqn', \App\Models\User::class),
        'role' => Bo\PermissionManager\App\Models\Role::class,
    ],

    /*
     * Table role
     */
    'table_roles'                      => 'roles',

    /*
     * Table model user has role , primary key table, foreign key for model_type
     */
    'table_model_has_roles'            => 'model_has_roles',
    'model_has_role_primary_key'       => 'role_id',
    'model_morph_key'                  => 'model_id',

    /*
     * ignore route don't check permission (by alias name route)
     * */
    'ignore_route_permission'          => [
        'bo.auth.login',
        "bo.auth.login.post",
        "bo.auth.logout",
        "bo.auth.logout.post",
        "bo.auth.register",
        "bo.auth.register.post",
        "bo.auth.password.reset",
        "bo.auth.password.reset.post",
        "bo.auth.password.reset.token",
        "bo.auth.password.email",
        "bo.dashboard",
        "bo",
        "bo.account.info",
        "bo.account.info.store",
        "bo.account.password",
    ],

    /*
    * ignore route don't check permission (by regex)
    * */
    'ignore_route_permission_by_regex' => [
        '.search',
        '.store',
        '.update',
        '.not_check',
    ],

    /*
    |--------------------------------------------------------------------------
    | Disallow the user interface for creating/updating permissions or roles.
    |--------------------------------------------------------------------------
    | Roles and permissions are used in code by their name
    | - ex: $user->hasPermissionTo('edit articles');
    |
    | So after the developer has entered all permissions and roles, the administrator should either:
    | - not have access to the panels
    | or
    | - creating and updating should be disabled
    */

    'allow_permission_create' => true,
    'allow_permission_update' => true,
    'allow_permission_delete' => true,
    'allow_role_create'       => true,
    'allow_role_update'       => true,
    'allow_role_delete'       => true,

    /*
    |--------------------------------------------------------------------------
    | Multiple-guards functionality
    |--------------------------------------------------------------------------
    |
    */
    'multiple_guards'         => true,

];
