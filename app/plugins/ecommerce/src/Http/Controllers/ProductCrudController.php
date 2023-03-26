<?php

namespace Bo\Ecommerce\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\BulkCloneOperation;
use Bo\Base\Http\Controllers\Operations\BulkDeleteOperation;
use Bo\Base\Http\Controllers\Operations\CloneOperation;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\InlineCreateOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Ecommerce\Http\Requests\ProductRequest;

class ProductCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use InlineCreateOperation;
    use CloneOperation;
    use BulkDeleteOperation;
    use BulkCloneOperation;

    public function setup()
    {
        CRUD::setModel("Bo\Ecommerce\Models\Product");
        CRUD::setRoute(config('bo.base.route_prefix', 'admin') . '/ec-product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['name']); // add multiple columns, at the end of the stack
        CRUD::addColumn([
            'name'          => 'status',
            'type'          => 'enum',
            'enum_function' => 'getReadableStatus',
        ]);
        CRUD::addColumn([
            'name'          => 'condition',
            'type'          => 'enum',
            'enum_class'    => 'Bo\Ecommerce\Enums\ProductCondition',
            'enum_function' => 'getReadableCondition',
        ]);
        CRUD::addColumn([
            'name'           => 'price',
            'type'           => 'number',
            'label'          => 'Price',
            'visibleInTable' => false,
            'visibleInModal' => true,
        ]);
        CRUD::addColumn([
            // 1-n relationship
            'label'          => 'Category', // Table column heading
            'type'           => 'select',
            'name'           => 'category_id', // the column that contains the ID of that connected entity;
            'entity'         => 'category', // the method that defines the relationship in your Model
            'attribute'      => 'name', // foreign key attribute that is shown to user
            'visibleInTable' => true,
            'visibleInModal' => false,
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => 'Name',
            'type'  => 'text',
            'tab'   => 'Texts',

            // optional
            //'prefix' => '',
            //'suffix' => '',
            //'default'    => 'some value', // default value
            //'hint'       => 'Some hint text', // helpful text, show up after input
            //'attributes' => [
            //'placeholder' => 'Some text when empty',
            //'class' => 'form-control some-class'
            //], // extra HTML attributes and values your input might need
            //'wrapperAttributes' => [
            //'class' => 'form-group col-md-12'
            //], // extra HTML attributes for the field wrapper - mostly for resizing fields
            //'readonly'=>'readonly',
        ]);

        CRUD::addField([   // Textarea
            'name'  => 'description',
            'label' => 'Description',
            'type'  => 'textarea',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([   // Wysiwyg
            'name'  => 'details',
            'label' => 'Details',
            'type'  => 'wysiwyg',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Table
            'name'            => 'features',
            'label'           => 'Features',
            'type'            => 'table',
            'entity_singular' => 'feature', // used on the "Add X" button
            'columns'         => [
                'name' => 'Feature',
                'desc' => 'Value',
            ],
            'max' => 25, // maximum rows allowed in the table
            'min' => 0, // minimum rows allowed in the table
            'tab' => 'Texts',
        ]);

        // Fake repeatable with translations
        CRUD::addField([ // Extra Features
            'name'      => 'extra_features',
            'label'     => 'Extra Features',
            'type'      => 'repeatable',
            'tab'       => 'Texts',
            'store_in'  => 'extras',
            'fake'      => true,
            'fields' => [
                [
                    'name'    => 'feature',
                    'wrapper' => [
                        'class' => 'col-md-3',
                    ],
                ],
                [
                    'name'    => 'value',
                    'wrapper' => [
                        'class' => 'col-md-6',
                    ],
                ],
                [
                    'name'    => 'quantity',
                    'type'    => 'number',
                    'wrapper' => [
                        'class' => 'col-md-3',
                    ],
                ],
            ],
        ]);

        CRUD::addField([  // Select2
            'label'     => 'Category',
            'type'      => 'select2',
            'name'      => 'category_id', // the db column for the foreign key
            'entity'    => 'category', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            // 'wrapperAttributes' => [
            //     'class' => 'form-group col-md-6'
            //   ], // extra HTML attributes for the field wrapper - mostly for resizing fields
            'tab' => 'Basic Info',
        ]);

        CRUD::addField([   // Number
            'name'  => 'price',
            'label' => 'Price',
            'type'  => 'number',
            // optionals
            // 'attributes' => ["step" => "any"], // allow decimals
            'prefix' => '$',
            'suffix' => '.00',
            // 'wrapperAttributes' => [
            //    'class' => 'form-group col-md-6'
            //  ], // extra HTML attributes for the field wrapper - mostly for resizing fields
            'tab' => 'Basic Info',
        ]);
        CRUD::addField([   // Number
            'name'  => 'status',
            'label' => 'Status',
            'type'  => 'enum',
            'tab'   => 'Basic Info',
        ]);
        CRUD::addField([   // Number
            'name'          => 'condition',
            'label'         => 'Condition',
            'type'          => 'enum',
            'tab'           => 'Basic Info',
            'enum_class'    => 'Bo\Ecommerce\Enums\ProductCondition',
            'enum_function' => 'getReadableCondition',
        ]);

        CRUD::addFields([
            [ // Text
                'name'  => 'meta_title',
                'label' => 'Meta Title',
                'type'  => 'text',
                'fake'  => true,
                'tab'   => 'Metas',
            ],
            [ // Text
                'name'  => 'meta_description',
                'label' => 'Meta Description',
                'type'  => 'text',
                'fake'  => true,
                'tab'   => 'Metas',
            ],
            [ // Text
                'name'  => 'meta_keywords',
                'label' => 'Meta Keywords',
                'type'  => 'text',
                'fake'  => true,
                'tab'   => 'Metas',
            ],
        ]);

        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
