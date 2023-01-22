<?php

namespace Bo\MenuItem\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;

class MenuItemController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\Bo\MenuItem\Models\MenuItem::class);
        CRUD::setRoute(config('bo.base.route_prefix') . '/menu-item');
        CRUD::setEntityNameStrings('menu-item', 'menu-items');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
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

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(\Bo\MenuItem\Http\Requests\MenuItemRequest::class);

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
            'view_namespace' => 'menu-item::fields',
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
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
