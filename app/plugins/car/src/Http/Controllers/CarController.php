<?php

namespace Bo\Car\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Car\Http\Requests\CarRequest;
use Bo\Car\Models\Car;

class CarController extends CrudController
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
        CRUD::setModel(Car::class);
        CRUD::setRoute(config('bo.base.route_prefix') . '/car');
        CRUD::setEntityNameStrings('Phương tiện', 'Phương tiện tai nạn');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('license_plates');
        CRUD::column('description');
        CRUD::addColumn([
            'name'     => 'source',
            'type'     => 'closure',
            'function' => function ($entry) {
                return "<a target='_blank' href='" . $entry->source . "'> $entry->source <i class='las la-external-link-alt'></i></a>";
            },
            'escaped'  => false
        ]);

        $this->crud->addColumns([
            [
                'name' => 'status',
                'type' => 'select_from_array',
                'options' => [
                    Car::PENDING_STATUS => 'pending',
                    Car::PUBLISH_STATUS => 'publish',
                ],
            ],
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CarRequest::class);

        CRUD::field('name');
        CRUD::field('description');
        CRUD::field('source');
        CRUD::field('status');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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
