<?php

namespace Bo\Blog\Http\Controllers;

use Bo\Base\Http\Controllers\CrudController;
use Bo\Blog\Http\Requests\ArticleRequest;

class ArticleCrudController extends CrudController
{
    use \Bo\Base\Http\Controllers\Operations\ListOperation;
    use \Bo\Base\Http\Controllers\Operations\CreateOperation;
    use \Bo\Base\Http\Controllers\Operations\UpdateOperation;
    use \Bo\Base\Http\Controllers\Operations\CloneOperation;
    use \Bo\Base\Http\Controllers\Operations\BulkCloneOperation;
    use \Bo\Base\Http\Controllers\Operations\DeleteOperation;
    use \Bo\Base\Http\Controllers\Operations\BulkDeleteOperation;
    use \Bo\Base\Http\Controllers\Operations\ShowOperation;
    use \Bo\Base\Http\Controllers\Operations\FetchOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel(\Bo\Blog\Models\Article::class);
        $this->crud->setRoute(config('bo.base.route_prefix', 'admin').'/article');
        $this->crud->setEntityNameStrings('article', 'articles');

        /*
        |--------------------------------------------------------------------------
        | LIST OPERATION
        |--------------------------------------------------------------------------
        */
        $this->crud->operation('list', function () {
            $this->crud->addColumn('title');
            $this->crud->addColumn([
                'name' => 'date',
                'label' => 'Date',
                'type' => 'date',
            ]);
            $this->crud->addColumn('status');
            $this->crud->addColumn([
                'name' => 'featured',
                'label' => 'Featured',
                'type' => 'check',
            ]);
            $this->crud->addColumn([
                'label' => 'Category',
                'type' => 'select',
                'name' => 'category_id',
                'entity' => 'category',
                'attribute' => 'name',
                'wrapper'   => [
                    'href' => function ($crud, $column, $entry, $related_key) {
                        return bo_url('category/'.$related_key.'/show');
                    },
                ],
            ]);
            $this->crud->addColumn('tags');

            $this->crud->addFilter([ // select2 filter
                'name' => 'category_id',
                'type' => 'select2',
                'label'=> 'Category',
            ], function () {
                return \Bo\Blog\Models\Category::all()->keyBy('id')->pluck('name', 'id')->toArray();
            }, function ($value) { // if the filter is active
                $this->crud->addClause('where', 'category_id', $value);
            });

            $this->crud->addFilter([ // select2_multiple filter
                'name' => 'tags',
                'type' => 'select2_multiple',
                'label'=> 'Tags',
            ], function () {
                return \Bo\Blog\Models\Tag::all()->keyBy('id')->pluck('name', 'id')->toArray();
            }, function ($values) { // if the filter is active
                $this->crud->query = $this->crud->query->whereHas('tags', function ($q) use ($values) {
                    foreach (json_decode($values) as $key => $value) {
                        if ($key == 0) {
                            $q->where('tags.id', $value);
                        } else {
                            $q->orWhere('tags.id', $value);
                        }
                    }
                });
            });
        });

        /*
        |--------------------------------------------------------------------------
        | CREATE & UPDATE OPERATIONS
        |--------------------------------------------------------------------------
        */
        $this->crud->operation(['create', 'update'], function () {
            $this->crud->setValidation(ArticleRequest::class);

            $this->crud->addField([
                'name' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'placeholder' => 'Your title here',
            ]);
            $this->crud->addField([
                'name' => 'slug',
                'label' => 'Slug (URL)',
                'type' => 'text',
                'hint' => 'Will be automatically generated from your title, if left empty.',
                // 'disabled' => 'disabled'
            ]);
            $this->crud->addField([
                'name' => 'date',
                'label' => 'Date',
                'type' => 'date',
                'default' => date('Y-m-d'),
            ]);
            $this->crud->addField([
                'name' => 'content',
                'label' => 'Content',
                'type' => 'ckeditor-shortcode',
                'placeholder' => 'Your textarea text here',
            ]);
            $this->crud->addField([
                'name' => 'image',
                'label' => 'Image',
                'type' => 'browse',
            ]);
            $this->crud->addField([
                'label' => 'Category',
                'type' => 'relationship',
                'name' => 'category_id',
                'entity' => 'category',
                'attribute' => 'name',
                'inline_create' => true,
                'ajax' => true,
            ]);
            $this->crud->addField([
                'label' => 'Tags',
                'type' => 'relationship',
                'name' => 'tags', // the method that defines the relationship in your Model
                'entity' => 'tags', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'inline_create' => ['entity' => 'tag'],
                'ajax' => true,
            ]);
            $this->crud->addField([
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select_from_array',
                'options' => [
                    'PUBLISHED' => 'PUBLISHED',
                    'DRAFT' => 'DRAFT',
                ],
            ]);
            $this->crud->addField([
                'name' => 'featured',
                'label' => 'Featured item',
                'type' => 'checkbox',
            ]);
        });
    }

    /**
     * Respond to AJAX calls from the select2 with entries from the Category model.
     *
     * @return JSON
     */
    public function fetchCategory()
    {
        return $this->fetch(\Bo\Blog\Models\Category::class);
    }

    /**
     * Respond to AJAX calls from the select2 with entries from the Tag model.
     *
     * @return JSON
     */
    public function fetchTags()
    {
        return $this->fetch(\Bo\Blog\Models\Tag::class);
    }
}
