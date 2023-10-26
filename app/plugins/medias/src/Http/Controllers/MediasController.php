<?php

namespace Bo\Medias\Http\Controllers;

use App\Libs\DiskPathTools\DiskPathInfo;
use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Car\Models\Car;
use Bo\Medias\Http\Requests\MediasRequest;
use Bo\Medias\Models\Medias;

class MediasController extends CrudController
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
        CRUD::setModel(Medias::class);
        CRUD::setRoute(config('bo.base.route_prefix') . '/medias');
        CRUD::setEntityNameStrings('medias', 'medias');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::addColumn([
            'name'     => 'source_id',
            'type'     => 'closure',
            'function' => function ($entry) {
                return "<a target='_blank' href='" . bo_url('car/' . $entry->source_id . '/show') . "'>" . Car::find($entry->source_id)->license_plates . "<i class='las la-external-link-alt'></i></a>";
            },
            'escaped'  => false
        ]);
        CRUD::column('type');
        CRUD::column('target');
        CRUD::column('target_data');
        CRUD::column('status')->type('number');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MediasRequest::class);

        CRUD::field('source_id');
        CRUD::field('type');
        CRUD::field('target');
        CRUD::field('target_data');
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

    protected function setupShowOperation()
    {
        $this->autoSetupShowOperation();

        CRUD::addColumn([
            'name'  => 'medias',
            'type'  => 'custom_html',
            'value' => function ($entry) {
                return '<img src="' . $entry->getMedia() . '" class="w-50"/>';
            }
        ]);
    }
}
