<?php

namespace Bo\Blog\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BlogsController
 * @package App\Http\Controllers\Admin
 * @property-read \Bo\Base\Library\CrudPanel\CrudPanel $crud
 */
class BlogsController extends CrudController
{
    use \Bo\Base\Http\Controllers\Operations\ListOperation;
    use \Bo\Base\Http\Controllers\Operations\CreateOperation;
    use \Bo\Base\Http\Controllers\Operations\UpdateOperation;
    use \Bo\Base\Http\Controllers\Operations\DeleteOperation;
    use \Bo\Base\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\Bo\Blog\Models\Blogs::class);
        CRUD::setRoute(config('bo.base.route_prefix') . '/blogs');
        CRUD::setEntityNameStrings('blogs', 'blogs');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // columns

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
        CRUD::setValidation(\Bo\Blog\Http\Requests\BlogsRequest::class);

        CRUD::setFromDb(); // fields

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
