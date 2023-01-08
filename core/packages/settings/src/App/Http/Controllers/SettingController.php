<?php

namespace Bo\Settings\App\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Settings\App\Http\Requests\SettingRequest;
use Bo\Settings\App\Models\Setting;

class SettingController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use CreateOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(Setting::class);
        CRUD::setEntityNameStrings(trans('setting::settings.setting_singular'), trans('setting::settings.setting_plural'));
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
                'label' => trans('setting::settings.name'),
            ],
            [
                'name'  => 'description',
                'label' => trans('setting::settings.description'),
            ],
            [
                'name'  => 'type',
                'label' => trans('setting::settings.type'),
            ],
        ]);
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(SettingRequest::class);
        $this->crud->setFromDb();
    }

    public function setupUpdateOperation()
    {
        $this->crud->setValidation(SettingRequest::class);
        $this->crud->setFromDb();
    }
}
