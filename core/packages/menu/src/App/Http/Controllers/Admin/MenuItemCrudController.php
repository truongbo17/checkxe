<?php

namespace Bo\MenuCRUD\App\Http\Controllers\Admin;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ReorderOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\MenuCRUD\App\Http\Requests\MenuRequests;
use Bo\MenuCRUD\App\Models\Menu;

class MenuItemCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation {
        CreateOperation::create as traitCreate;
    }
    use UpdateOperation;
    use DeleteOperation;
    use ReorderOperation;

    public function setup()
    {
        $this->crud->setModel("Bo\MenuCRUD\App\Models\MenuItem");
        $this->crud->setRoute(config('bo.base.route_prefix') . '/menu-item');
        $this->crud->setEntityNameStrings('menu item', 'menu items');

        $this->crud->enableReorder('name', 10);

        $this->crud->operation(['create', 'update'], function () {
            $this->crud->addField([
                'name' => 'name',
                'label' => 'Label',
            ]);
            $this->crud->addField([
                'label'     => 'Parent',
                'type'      => 'select',
                'name'      => 'parent_id',
                'entity'    => 'parent',
                'attribute' => 'name',
                'model'     => "\Bo\MenuCRUD\App\Models\MenuItem",
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
        $menu = Menu::findOrFail(request()->input('menu-id'));

        $menu_item_id = $menu->menuItems()->get()->pluck('id')->toArray();

        $this->crud->addClause('whereIn', 'id', $menu_item_id);

        $this->crud->operation('list', function () {
            $this->crud->addColumn([
                'name'  => 'name',
                'label' => 'Label',
            ]);
            $this->crud->addColumn([
                'label'     => 'Parent',
                'type'      => 'select',
                'name'      => 'parent_id',
                'entity'    => 'parent',
                'attribute' => 'name',
                'model'     => "\Bo\MenuCRUD\App\Models\MenuItem",
            ]);
        });
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(MenuRequests::class);
    }
}
