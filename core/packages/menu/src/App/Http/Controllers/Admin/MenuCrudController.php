<?php

namespace Bo\MenuCRUD\App\Http\Controllers\Admin;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\MenuCRUD\App\Http\Requests\MenuRequests;

class MenuCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation {
        CreateOperation::create as traitCreate;
    }
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        $this->crud->setModel("Bo\MenuCRUD\App\Models\Menu");
        $this->crud->setRoute(config('bo.base.route_prefix') . '/menu');
        $this->crud->setEntityNameStrings('menu', 'menus');

        $this->crud->operation(['create', 'update'], function () {
            $this->crud->addField([
                'name'  => 'name',
                'label' => 'Name',
            ]);
            $this->crud->addField([
                'name'  => 'key',
                'label' => 'Key',
            ]);
            $this->crud->addField([
                'name'  => 'description',
                'label' => 'Description',
            ]);
            $this->crud->addField([
                'name'           => 'item',
                'type'           => 'select-menu-item',
                'label'          => 'Menu Item',
                'view_namespace' => 'menucrud::fields',
                'fields'         => [
                    [
                        'name'    => 'menu-item-id',
                        'type'    => 'select_and_order',
                        'label'   => 'Name',
                        'options' => [
                            'test'      => 'a',
                                'wtest' => 'wa',
                                'twest' => 'wwa',
                            ],
                        ],
                    ],
                ]);
        });
    }

    public function setupListOperation()
    {
        $this->crud->addColumn([
            'name'  => 'key',
            'label' => 'Key',
        ]);
        $this->crud->addColumn([
            'name'  => 'name',
            'label' => 'Label',
        ]);
        $this->crud->addColumn([
            'name'  => 'description',
            'label' => 'Description',
        ]);
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(MenuRequests::class);
    }

    public function setupUpdateOperation()
    {
        $this->crud->setValidation(MenuRequests::class);
    }

    /**
     * Update the specified resource in the database.
     *
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // update the row in the db
        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest($request)
        );
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('bo::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
