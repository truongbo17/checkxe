<?php

namespace Bo\PermissionManager\App\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\PermissionManager\App\Enum\IsAdminEnum;
use Bo\PermissionManager\App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Bo\PermissionManager\App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Support\Facades\Hash;

class UserCrudController extends CrudController
{
    use \Bo\Base\Http\Controllers\Operations\ListOperation;
    use \Bo\Base\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Bo\Base\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Bo\Base\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        $this->crud->setModel(config('bo.permissionmanager.models.user'));
        $this->crud->setEntityNameStrings(trans('bo::permissionmanager.user'), trans('bo::permissionmanager.users'));
        $this->crud->setRoute(bo_url('user'));
    }

    public function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name'  => 'name',
                'label' => trans('bo::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('bo::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'    => 'is_admin',
                'label'   => 'Admin',
                'type'    => 'select_from_array',
                'options' => array_flip(IsAdminEnum::asArray()),
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('bo::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => config('bo.permissionmanager.models.role'), // foreign key model
            ]
        ]);

        // Role Filter
        $this->crud->addFilter(
            [
                'name'  => 'role',
                'type'  => 'dropdown',
                'label' => trans('bo::permissionmanager.role'),
            ],
            config('bo.permissionmanager.models.role')::all()->pluck('name', 'id')->toArray(),
            function ($value) { // if the filter is active
                $this->crud->addClause('whereHas', 'roles', function ($query) use ($value) {
                    $query->where('role_id', '=', $value);
                });
            }
        );

        //Set and remove user admin
        $this->crud->addButtonFromView('line', 'moderate', 'change_status_admin', 'beginning');


    }

    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);
    }

    public function setupUpdateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        if ($this->crud->getRequest()->has('roles')) {
            $this->crud->getRequest()->merge(['is_admin' => IsAdminEnum::IS_ADMIN]);
        }else{
            $this->crud->getRequest()->merge(['is_admin' => IsAdminEnum::NOT_ADMIN]);
        }

        return $this->traitStore();
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        if ($this->crud->getRequest()->has('roles')) {
            $this->crud->getRequest()->merge(['is_admin' => IsAdminEnum::IS_ADMIN]);
        }else{
            $this->crud->getRequest()->merge(['is_admin' => IsAdminEnum::NOT_ADMIN]);
        }

        return $this->traitUpdate();
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('bo::permissionmanager.name'),
                'type'  => 'text',
                'tab'   => 'Base info',
            ],
            [
                'name'  => 'email',
                'label' => trans('bo::permissionmanager.email'),
                'type'  => 'email',
                'tab'   => 'Base info',
            ],
            [
                'name'  => 'password',
                'label' => trans('bo::permissionmanager.password'),
                'type'  => 'password',
                'tab'   => 'Base info',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('bo::permissionmanager.password_confirmation'),
                'type'  => 'password',
                'tab'   => 'Base info',
            ],
            [
                'name'  => 'is_admin',
                'type'  => 'number',
                'tab'   => 'Base info',
                'attributes' => [
                    'placeholder' => 'Auto',
                    'readonly'    => 'readonly',
                    'disabled'    => 'disabled',
                ],
            ],
            [
                'name'  => 'roles',
                'label' => 'Chose role',
                'type'  => 'custom_role_select2',
                'tab'   => 'Permission',
            ],
        ]);
    }

    public function traitUpdate()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest($request)
        );
        if (!$request->roles) {
            //remove all roles relation of user
            $user = $this->crud->model->findOrFail($request->get($this->crud->model->getKeyName()));
            $user->roles()->detach();
        }

        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('bo::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    public function updateStatusAdmin(int $id): bool
    {
        $user = $this->crud->model->findOrFail($id);

        if ($user->is_admin == IsAdminEnum::IS_ADMIN) {
            $status = IsAdminEnum::NOT_ADMIN;
        } else {
            $status = IsAdminEnum::IS_ADMIN;
        }

        $user->update([
            'is_admin' => $status,
        ]);

        return true;
    }
}
