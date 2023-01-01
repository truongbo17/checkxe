<?php

namespace Bo\LangFileManager\App\Http\Controllers;

use Alert;
use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\CreateOperation;
use Bo\Base\Http\Controllers\Operations\DeleteOperation;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
use Bo\Base\Http\Controllers\Operations\UpdateOperation;
use Bo\LangFileManager\App\Http\Requests\LanguageRequest;
use Bo\LangFileManager\App\Models\Language;
use Bo\LangFileManager\App\Services\LangFiles;
use File;
use Illuminate\Http\Request;

class LanguageCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation {
        CreateOperation::store as traitStore;
    }
    use UpdateOperation;
    use DeleteOperation {
        DeleteOperation::destroy as traitDestroy;
    }
    use ShowOperation;

    public function setup()
    {
        $this->crud->setModel("Bo\LangFileManager\App\Models\Language");
        $this->crud->setRoute(config('bo.base.route_prefix', 'admin') . '/language');
        $this->crud->setEntityNameStrings(trans('bo::langfilemanager.language'), trans('bo::langfilemanager.languages'));
    }

    public function setupListOperation()
    {
        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('bo::langfilemanager.language_name'),
            ],
            [
                'name'  => 'active',
                'label' => trans('bo::langfilemanager.active'),
                'type'  => 'boolean',
            ],
            [
                'name'  => 'default',
                'label' => trans('bo::langfilemanager.default'),
                'type'  => 'boolean',
            ],
        ]);
        $this->crud->addButton('line', 'translate', 'view', 'langfilemanager::button', 'beginning');
    }

    public function setupUpdateOperation()
    {
        return $this->setupCreateOperation();
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(LanguageRequest::class);
        $this->crud->addField([
            'name'  => 'name',
            'label' => trans('bo::langfilemanager.language_name'),
            'type'  => 'text',
        ]);
        $this->crud->addField([
            'name'  => 'native',
            'label' => trans('bo::langfilemanager.native_name'),
            'type'  => 'text',
        ]);
        $this->crud->addField([
            'name'  => 'abbr',
            'label' => trans('bo::langfilemanager.code_iso639-1'),
            'type'  => 'text',
        ]);
        $this->crud->addField([
            'name'  => 'flag',
            'label' => trans('bo::langfilemanager.flag_image'),
            'type'  => bopro() ? 'browse' : 'text',
        ]);
        $this->crud->addField([
            'name'  => 'active',
            'label' => trans('bo::langfilemanager.active'),
            'type'  => 'checkbox',
        ]);
        $this->crud->addField([
            'name'  => 'default',
            'label' => trans('bo::langfilemanager.default'),
            'type'  => 'checkbox',
        ]);
    }

    public function store()
    {
        $defaultLang = Language::where('default', 1)->first();

        // Copy the default language folder to the new language folder
        File::copyDirectory(resource_path('lang/' . $defaultLang->abbr), resource_path('lang/' . request()->input('abbr')));

        return $this->traitStore();
    }

    /**
     * After delete remove also the language folder.
     *
     * @param int $id
     * @return string
     */
    public function destroy($id)
    {
        $language = Language::find($id);
        $destroyResult = $this->traitDestroy($id);

        if ($destroyResult) {
            File::deleteDirectory(resource_path('lang/' . $language->abbr));
        }

        return $destroyResult;
    }

    public function showTexts(LangFiles $langfile, Language $languages, $lang = '', $file = 'site')
    {
        // SECURITY
        // check if that file isn't forbidden in the config file
        if (in_array($file, config('bo.langfilemanager.language_ignore'))) {
            abort('403', trans('bo::langfilemanager.cant_edit_online'));
        }

        if ($lang) {
            $langfile->setLanguage($lang);
        }

        $langfile->setFile($file);
        $this->data['crud'] = $this->crud;
        $this->data['currentFile'] = $file;
        $this->data['currentLang'] = $lang ?: config('app.locale');
        $this->data['currentLangObj'] = Language::where('abbr', '=', $this->data['currentLang'])->first();
        $this->data['browsingLangObj'] = Language::where('abbr', '=', config('app.locale'))->first();
        $this->data['languages'] = $languages->orderBy('name')->where('active', 1)->get();
        $this->data['langFiles'] = $langfile->getlangFiles();
        $this->data['fileArray'] = $langfile->getFileContent();
        $this->data['langfile'] = $langfile;
        $this->data['title'] = trans('bo::langfilemanager.translations');

        return view('langfilemanager::translations', $this->data);
    }

    public function updateTexts(LangFiles $langfile, Request $request, $lang = '', $file = 'site')
    {
        // SECURITY
        // check if that file isn't forbidden in the config file
        if (in_array($file, config('bo.langfilemanager.language_ignore'))) {
            abort('403', trans('bo::langfilemanager.cant_edit_online'));
        }

        $message = trans('error.error_general');
        $status = false;

        if ($lang) {
            $langfile->setLanguage($lang);
        }

        $langfile->setFile($file);

        $fields = $langfile->testFields($request->all());
        if (empty($fields)) {
            if ($langfile->setFileContent($request->all())) {
                Alert::success(trans('bo::langfilemanager.saved'))->flash();
                $status = true;
            }
        } else {
            $message = trans('admin.language.fields_required');
            Alert::error(trans('bo::langfilemanager.please_fill_all_fields'))->flash();
        }

        return redirect()->back();
    }
}
