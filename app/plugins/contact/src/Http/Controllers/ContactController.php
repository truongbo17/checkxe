<?php

namespace Bo\Contact\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Contact\Enum\ContactStatusEnum;

class ContactController extends CrudController
{
    use ListOperation;
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
        CRUD::setModel(\Bo\Contact\Models\Contact::class);
        CRUD::setRoute(config('bo.base.route_prefix') . '/contact');
        CRUD::setEntityNameStrings('liên hệ', 'Liên hệ');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'id',
                'type' => 'text'
            ],
            [
                'label' => 'Họ tên',
                'name' => 'name',
            ],
            [
                'name' => 'email',
                'label' => 'Email',
            ],
            [
                'name' => 'content',
                'label' => 'Nội dung',
            ],
            [
                'name' => 'status',
                'type' => 'select_from_array',
                'options' => array_flip(ContactStatusEnum::asArray()),
            ],
            [
                'name' => 'created_at',
                'type' => 'datetime',
            ],
        ]);

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Filter Status'
        ], array_flip(ContactStatusEnum::asArray()), function ($value) {
            $this->crud->addClause('where', 'status', $value);
        });
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
