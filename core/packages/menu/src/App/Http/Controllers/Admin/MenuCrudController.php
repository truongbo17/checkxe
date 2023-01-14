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
                'name'  => 'description',
                'label' => 'Description',
            ]);
        });
    }

    public function setupListOperation()
    {
        $this->crud->addColumn([
            'name'  => 'name',
            'label' => 'Label',
        ]);
        $this->crud->addColumn([
            'name'  => 'description',
            'label' => 'Description',
        ]);

        $this->crud->addButtonFromModelFunction('line', 'menu-item', 'gotoMenuItem', 'beginning');
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(MenuRequests::class);
    }
}
