<?php

namespace Bo\Shortcode\App\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Shortcode\App\Http\Requests\ShortcodeRequest;
use Bo\Shortcode\App\Models\Shortcode;

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

        $this->crud->addField([
            'name'  => 'name',
            'label' => trans('bo::shortcodes.name'),
            'type'  => 'text',
        ]);

        $this->crud->addField([
            'name'  => 'key',
            'label' => trans('bo::shortcodes.key'),
            'type'  => 'text',
        ]);

        $this->crud->addField([
            'name'  => 'type',
            'label' => trans('bo::shortcodes.type'),
            'type'  => 'select2_from_array',
            'options' => [
                'source' => 'Source',
                'view'   => 'View',
            ],
        ]);

        $this->crud->addField([
            'name'         => 'value',
            'label'        => trans('bo::shortcodes.value'),
            'type'         => 'custom_shortcode',
            'options_view' => get_all_short_code_in_view()
        ]);

        $this->crud->addField([   // repeatable
            'name'           => 'option',
            'label'          => 'Option pass variable to view',
            'type'           => 'repeatable',
            'fields'         => [
                [
                    'name'       => 'key_variable',
                    'label'      => 'Name Variable',
                    'attributes' => [
                        'placeholder' => 'Please enter your name variable',
                    ],
                    'wrapper'    => ['class' => 'form-group col-md-5'],
                ],
                [
                    'name'       => 'value_variable',
                    'label'      => 'Value Variable',
                    'attributes' => [
                        'placeholder' => 'Please enter your value variable',
                    ],
                    'wrapper'    => ['class' => 'form-group col-md-7'],
                ],
            ],
            'new_item_label' => 'Add Line', // customize the text of the button
        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
