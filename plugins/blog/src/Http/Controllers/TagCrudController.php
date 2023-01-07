<?php

namespace Bo\Blog\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Blog\Http\Requests\TagRequest;

class TagCrudController extends CrudController
{
    use \Bo\Base\Http\Controllers\Operations\ListOperation;
    use \Bo\Base\Http\Controllers\Operations\CreateOperation;
    use \Bo\Base\Http\Controllers\Operations\UpdateOperation;
    use \Bo\Base\Http\Controllers\Operations\DeleteOperation;
    use \Bo\Base\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        $this->crud->setModel("Bo\Blog\Models\Tag");
        $this->crud->setRoute(config('bo.base.route_prefix', 'admin').'/tag');
        $this->crud->setEntityNameStrings('tag', 'tags');
        $this->crud->setFromDb();
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(TagRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->crud->setValidation(TagRequest::class);
    }
}
