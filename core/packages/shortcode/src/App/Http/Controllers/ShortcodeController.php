<?php

namespace Bo\Shortcode\App\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Shortcode\App\Models\Shortcode;
use Bo\Shortcode\App\Http\Requests\ShortcodeRequest;

class ShortcodeController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use CreateOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(Shortcode::class);
        CRUD::setEntityNameStrings(trans('bo::shortcodes.setting_singular'), trans('bo::shortcodes.setting_plural'));
        CRUD::setRoute(bo_url("shortcode"));
    }


    public function setupListOperation()
    {
        // columns to show in the table view
        CRUD::setColumns([
            [
                'name'  => 'name',
                'label' => trans('bo::shortcodes.name'),
            ],
            [
                'name'  => 'key',
                'label' => trans('bo::shortcodes.key'),
            ],
            [
                'name'  => 'type',
                'label' => trans('bo::shortcodes.type'),
            ],
        ]);
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(ShortcodeRequest::class);
        $this->crud->setFromDb();
    }

    public function setupUpdateOperation()
    {
        $this->crud->setValidation(ShortcodeRequest::class);
        $this->crud->setFromDb();
    }
}
