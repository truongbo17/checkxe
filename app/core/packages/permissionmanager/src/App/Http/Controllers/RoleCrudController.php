<?php

namespace Bo\PermissionManager\App\Http\Controllers;

use Alert;
use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\PermissionManager\App\Http\Requests\RoleStoreCrudRequest as StoreRequest;
use Bo\PermissionManager\App\Http\Requests\RoleUpdateCrudRequest as UpdateRequest;
use Illuminate\Http\RedirectResponse;
use Route;

// VALIDATION

class RoleCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        $this->role_model = $role_model = config('bo.permissionmanager.models.role');

        $this->crud->setModel($role_model);
        $this->crud->setEntityNameStrings(trans('permissionmanager::permissionmanager.role'), trans('permissionmanager::permissionmanager.roles'));
        $this->crud->setRoute(bo_url('role'));
    }

    public function setupListOperation()
    {
        /**
         * Show a column for the name of the role.
         */
        $this->crud->addColumn([
            'name'  => 'name',
            'label' => trans('permissionmanager::permissionmanager.name'),
            'type'  => 'text',
        ]);

        /**
         * Show a column with the number of users that have that particular role.
         *
         * Note: To account for the fact that there can be thousands or millions
         * of users for a role, we did not use the `relationship_count` column,
         * but instead opted to append a fake `user_count` column to
         * the result, using Laravel's `withCount()` method.
         * That way, no users are loaded.
         */
        $this->crud->query->withCount('users');
        $this->crud->addColumn([
            'label'   => trans('permissionmanager::permissionmanager.users'),
            'type'    => 'text',
            'name'    => 'users_count',
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return bo_url('user?role=' . $entry->getKey());
                },
            ],
            'suffix'  => ' ' . strtolower(trans('permissionmanager::permissionmanager.users')),
        ]);
    }

    public function setupCreateOperation()
    {
        $this->addFields();
        $this->crud->setValidation(StoreRequest::class);
    }

    private function addFields()
    {
        $this->crud->addField([
            'name'  => 'name',
            'label' => trans('permissionmanager::permissionmanager.name'),
            'type'  => 'text',
        ]);

        $this->crud->addField([
            'name'           => 'list_route_admin',
            'label'          => 'Add Route Permission',
            'type'           => 'custom_list_route',
            'new_item_label' => 'Add Line',
            'init_rows'      => 1,
            'fields'         => [
                [
                    'name'    => 'route_link',
                    'label'   => 'Route link url',
                    'wrapper' => ['class' => 'form-group col-md-12'],
                    'type'    => 'select2_from_array',
                    'options' => getRouteListAdmin(),
                ],
                [
                    'name'       => 'route_name',
                    'label'      => 'Route name',
                    'attributes' => [
                        'placeholder' => 'Make by route name alias',
                        'readonly'    => 'readonly',
                        'disabled'    => 'disabled',
                    ],
                    'wrapper'    => ['class' => 'form-group col-md-5'],
                ],
                [
                    'name'       => 'route_function',
                    'label'      => 'Route function',
                    'attributes' => [
                        'placeholder' => 'Its function route',
                        'readonly'    => 'readonly',
                        'disabled'    => 'disabled',
                    ],
                    'wrapper'    => ['class' => 'form-group col-md-7'],
                ],
                [
                    'name'       => 'route_link_input',
                    'attributes' => [
                        'placeholder' => 'Its function route',
                        'readonly'    => 'readonly',
                        'disabled'    => 'disabled',
                    ],
                ],
            ],
        ]);
    }

    public function setupUpdateOperation()
    {
        $this->addFields();
        $this->crud->setValidation(UpdateRequest::class);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return RedirectResponse
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $array_route_permission = json_decode($request->input('list_route_admin'), true);
        $array_route_permission = array_unique($array_route_permission, SORT_REGULAR);
        $request->merge(['list_route_admin' => json_encode($array_route_permission)]);

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // insert item in the db
        $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        Alert::success(trans('bo::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
