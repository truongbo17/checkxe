<?php

namespace Bo\Blog\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\InlineCreateOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ReorderOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\Base\Library\CrudPanel\CrudPanelFacade as CRUD;
use Bo\Blog\Http\Requests\CategoryRequest;

class CategoryCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ReorderOperation;
    use ShowOperation;
    use InlineCreateOperation;

    public function setup()
    {
        CRUD::setModel("Bo\Blog\Models\Category");
        CRUD::setRoute(config('bo.base.route_prefix', 'admin') . '/category');
        CRUD::setEntityNameStrings('category', 'categories');
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();

        CRUD::addColumn('created_at');
        CRUD::addColumn('updated_at');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn('name');
        CRUD::addColumn('slug');
        CRUD::addColumn('parent');
        CRUD::addColumn([   // select_multiple: n-n relationship (with pivot table)
            'label' => 'Articles', // Table column heading
            'type' => 'relationship_count',
            'name' => 'articles', // the method that defines the relationship in your Model
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return bo_url('article?category_id=' . $entry->getKey());
                },
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CategoryRequest::class);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Name',
        ]);
        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug (URL)',
            'type' => 'text',
            'hint' => 'Will be automatically generated from your name, if left empty.',
            // 'disabled' => 'disabled'
        ]);
        CRUD::addField([
            'label' => 'Parent',
            'type' => 'select',
            'name' => 'parent_id',
            'entity' => 'parent',
            'attribute' => 'name',
        ]);
    }

    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'name');
        CRUD::set('reorder.max_level', 2);
    }
}
