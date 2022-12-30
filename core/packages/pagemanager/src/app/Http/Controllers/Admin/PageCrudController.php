<?php

namespace Bo\PageManager\app\Http\Controllers\Admin;

use Bo\PageManager\app\PageTemplates;
use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\PageManager\app\Http\Requests\PageRequest;
use ReflectionClass;
use ReflectionMethod;
use Request;
use Str;

// VALIDATION: change the requests to match your own file names if you need form validation

class PageCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation {
        CreateOperation::create as traitCreate;
    }
    use UpdateOperation {
        UpdateOperation::edit as traitEdit;
    }
    use DeleteOperation;
    use PageTemplates;

    public function setup()
    {
        $this->crud->setModel(config('bo.pagemanager.page_model_class', 'Bo\PageManager\app\Models\Page'));
        $this->crud->setRoute(config('bo.base.route_prefix') . '/page');
        $this->crud->setEntityNameStrings(trans('bo::pagemanager.page'), trans('bo::pagemanager.pages'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name'  => 'name',
            'label' => trans('bo::pagemanager.name'),
        ]);
        $this->crud->addColumn([
            'name'          => 'template',
            'label'         => trans('bo::pagemanager.template'),
            'type'          => 'model_function',
            'function_name' => 'getTemplateName',
        ]);
        $this->crud->addColumn([
            'name'  => 'slug',
            'label' => trans('bo::pagemanager.slug'),
        ]);
        $this->crud->addButtonFromModelFunction('line', 'open', 'getOpenButton', 'beginning');
    }

    // -----------------------------------------------
    // Overwrites of CrudController
    // -----------------------------------------------

    protected function setupCreateOperation()
    {
        // Note:
        // - default fields, that all templates are using, are set using $this->addDefaultPageFields();
        // - template-specific fields are set per-template, in the PageTemplates trait;

        $this->addDefaultPageFields(Request::input('template'));
        $this->useTemplate(Request::input('template'));

        $this->crud->setValidation(PageRequest::class);
    }

    /**
     * Populate the create/update forms with basic fields, that all pages need.
     *
     * @param string $template The name of the template that should be used in the current form.
     */
    public function addDefaultPageFields($template = false)
    {
        $this->crud->addField([
            'name'              => 'template',
            'label'             => trans('bo::pagemanager.template'),
            'type'              => 'select_page_template',
            'view_namespace'    => file_exists(resource_path('views/vendor/bo/crud/fields/select_page_template.blade.php')) ? null : 'pagemanager::fields',
            'options'           => $this->getTemplatesArray(),
            'value'             => $template,
            'allows_null'       => false,
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);
        $this->crud->addField([
            'name'              => 'name',
            'label'             => trans('bo::pagemanager.page_name'),
            'type'              => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
            // 'disabled' => 'disabled'
        ]);
        $this->crud->addField([
            'name'  => 'title',
            'label' => trans('bo::pagemanager.page_title'),
            'type'  => 'text',
            // 'disabled' => 'disabled'
        ]);
        $this->crud->addField([
            'name'  => 'slug',
            'label' => trans('bo::pagemanager.page_slug'),
            'type'  => 'text',
            'hint'  => trans('bo::pagemanager.page_slug_hint'),
            // 'disabled' => 'disabled'
        ]);
    }

    // -----------------------------------------------
    // Methods that are particular to the PageManager.
    // -----------------------------------------------

    /**
     * Get all defined template as an array.
     *
     * Used to populate the template dropdown in the create/update forms.
     */
    public function getTemplatesArray()
    {
        $templates = $this->getTemplates();

        foreach ($templates as $template) {
            $templates_array[$template->name] = str_replace('_', ' ', Str::title($template->name));
        }

        return $templates_array;
    }

    /**
     * Get all defined templates.
     */
    public function getTemplates($template_name = false)
    {
        $templates_array = [];

        $templates_trait = new ReflectionClass('Bo\PageManager\app\PageTemplates');
        $templates = $templates_trait->getMethods(ReflectionMethod::IS_PRIVATE);

        if (!count($templates)) {
            abort(503, trans('bo::pagemanager.template_not_found'));
        }

        return $templates;
    }

    /**
     * Add the fields defined for a specific template.
     *
     * @param string $template_name The name of the template that should be used in the current form.
     */
    public function useTemplate($template_name = false)
    {
        $templates = $this->getTemplates();

        // set the default template
        if ($template_name == false) {
            $template_name = $templates[0]->name;
        }

        // actually use the template
        if ($template_name) {
            $this->{$template_name}();
        }
    }

    protected function setupUpdateOperation()
    {
        // if the template in the GET parameter is missing, figure it out from the db
        $template = Request::input('template') ?? $this->crud->getCurrentEntry()->template;

        $this->addDefaultPageFields($template);
        $this->useTemplate($template);

        $this->crud->setValidation(PageRequest::class);
    }
}
