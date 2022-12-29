<?php

namespace Bo\Settings\App\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;

class SettingController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use CreateOperation;

    public function setup()
    {
        CRUD::setModel("Bo\Settings\App\Models\Setting");
        CRUD::setEntityNameStrings(trans('bo::settings.setting_singular'), trans('bo::settings.setting_plural'));
        CRUD::setRoute(bo_url(config('bo.setting.route')));
    }


    public function setupListOperation()
    {
        // only show settings which are marked as active
        CRUD::addClause('where', 'active', 1);

        // columns to show in the table view
        CRUD::setColumns([
            [
                'name'  => 'name',
                'label' => trans('bo::settings.name'),
            ],
            [
                'name'  => 'value',
                'label' => trans('bo::settings.value'),
            ],
            [
                'name'  => 'description',
                'label' => trans('bo::settings.description'),
            ],
            [
                'name'  => 'type',
                'label' => trans('bo::settings.type'),
            ],
        ]);
    }

    public function setupUpdateOperation()
    {
        CRUD::addField([
            'name'       => 'name',
            'label'      => trans('bo::settings.name'),
            'type'       => 'text',
            'attributes' => [
                'disabled' => 'disabled',
            ],
        ]);

        CRUD::addField(json_decode(CRUD::getCurrentEntry()->field, true));
    }
}
