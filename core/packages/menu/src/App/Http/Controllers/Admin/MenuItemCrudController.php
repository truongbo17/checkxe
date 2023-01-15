<?php

namespace Bo\MenuCRUD\App\Http\Controllers\Admin;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\MenuCRUD\App\Http\Requests\MenuItemRequests;

class MenuItemCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation {
        CreateOperation::create as traitCreate;
    }
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        $this->crud->setModel("Bo\MenuCRUD\App\Models\MenuItem");
        $this->crud->setRoute(config('bo.base.route_prefix') . '/menu-item');
        $this->crud->setEntityNameStrings('menu item', 'menu items');

        $this->crud->operation(['create', 'update'], function () {
            $this->crud->addField([
                'name'  => 'name',
                'label' => 'Label',
            ]);
            $this->crud->addField([
                'name'           => ['type', 'link', 'page_id', 'router_name'],
                'label'          => 'Type',
                'type'           => 'page_or_link',
                'page_model'     => '\Bo\PageManager\App\Models\Page',
                'view_namespace' => file_exists(resource_path('views/vendor/bo/crud/fields/page_or_link.blade.php')) ? null : 'menucrud::fields',
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
            'name'    => 'type',
            'label'   => 'Type',
            'type'    => 'select_from_array',
            'options' => [
                'page_link'     => 'Page link',
                'internal_link' => 'Internal link',
                'external_link' => 'External link',
                'router_name'   => 'Router name'
            ],
        ]);
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(MenuItemRequests::class);
    }
}
