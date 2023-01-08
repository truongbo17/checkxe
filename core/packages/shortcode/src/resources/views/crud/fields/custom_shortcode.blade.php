<!-- CKeditor -->
@php
    $field['extra_plugins'] = isset($field['extra_plugins']) ? implode(',', $field['extra_plugins']) : "embed,widget";

    $defaultOptions = [
        "filebrowserBrowseUrl" => bo_url('elfinder/ckeditor'),
        "extraPlugins" => $field['extra_plugins'],
        "embed_provider" => "//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}",
    ];

    $field['value'] = old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '';

    $field['options'] = array_merge($defaultOptions, $field['options'] ?? []);
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')
<textarea
    id="{{ $field['name'] }}"
    data-init-function="bpFieldInitCKEditorElement"
    data-options="{{ trim(json_encode($field['options'])) }}"
        @include('crud::fields.inc.attributes', ['default_class' => 'form-control'])
    	>{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}</textarea>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

<select
    id="{{ $field['name'] }}"
    style="width: 100%"
    data-init-function="bpFieldInitSelect2FromArrayElement"
    data-field-is-inline="{{var_export($inlineCreate ?? false)}}"
    data-language="{{ str_replace('_', '-', app()->getLocale()) }}"
    @include('crud::fields.inc.attributes', ['default_class' =>  'form-control select2_from_array'])
>
    @if (count($field['options_view']))
        @foreach ($field['options_view'] as $key => $value)
            @if((old(square_brackets_to_dots($field['name'])) !== null && (
                    $key == old(square_brackets_to_dots($field['name'])) ||
                    (is_array(old(square_brackets_to_dots($field['name']))) &&
                    in_array($key, old(square_brackets_to_dots($field['name'])))))) ||
                    (null === old(square_brackets_to_dots($field['name'])) &&
                        ((isset($field['value']) && (
                                    $key == $field['value'] || (
                                            is_array($field['value']) &&
                                            in_array($key, $field['value'])
                                            )
                                    )) ||
                            (!isset($field['value']) && isset($field['default']) &&
                            ($key == $field['default'] || (
                                            is_array($field['default']) &&
                                            in_array($key, $field['default'])
                                        )
                                    )
                            ))
                    ))
                <option value="{{ $key }}" selected>{{ $value }}</option>
            @else
                <option value="{{ $key }}">{{ $value }}</option>
            @endif
        @endforeach
    @endif
</select>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

@push('crud_fields_scripts')
    <script>
        $('textarea#{{ $field['name'] }}').parent().hide();
        $('select#{{ $field['name'] }}').parent().hide();

        selectType($("select[name=type]").val());

        $("select[name=type]").on('change', function (e) {
            selectType(e.target.value);
        });

        function selectType(type) {
            if (type.toLowerCase() === 'source') {
                $('textarea#{{ $field['name'] }}').parent().show();
                $('textarea#{{ $field['name'] }}').attr('name', '{{ $field['name'] }}');
                $('select#{{ $field['name'] }}').parent().hide();
                $('select#{{ $field['name'] }}').removeAttr('name');
            } else {
                $('select#{{ $field['name'] }}').parent().show();
                $('select#{{ $field['name'] }}').attr('name', '{{ $field['name'] }}');
                $('textarea#{{ $field['name'] }}').parent().hide();
                $('textarea#{{ $field['name'] }}').removeAttr('name');
            }
        }
    </script>
@endpush

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- include select2 css-->
        <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet"
              type="text/css"/>
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- include select2 js-->
        <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
        @if (app()->getLocale() !== 'en')
            <script
                src="{{ asset('packages/select2/dist/js/i18n/' . str_replace('_', '-', app()->getLocale()) . '.js') }}"></script>
        @endif
        <script>
            function bpFieldInitSelect2FromArrayElement(element) {
                if (!element.hasClass("select2-hidden-accessible")) {
                    let $isFieldInline = element.data('field-is-inline');

                    element.select2({
                        theme: "bootstrap",
                        dropdownParent: $isFieldInline ? $('#inline-create-dialog .modal-content') : document.body
                    }).on('select2:unselect', function (e) {
                        if ($(this).attr('multiple') && $(this).val().length == 0) {
                            $(this).val(null).trigger('change');
                        }
                    });
                }
            }
        </script>
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script src="{{ asset('packages/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('packages/ckeditor/adapters/jquery.js') }}"></script>
        <script>
            $('.select2').select2();

            function bpFieldInitCKEditorElement(element) {


                //when removing ckeditor field from page html the instance is not properly deleted.
                //this event is triggered in repeatable on deletion so this field can intercept it
                //and properly delete the instances so it don't throw errors of unexistent elements in page that has initialized ck instances.
                element.on('bo_field.deleted', function(e) {
                    $ck_instance_name = element.siblings("[id^='cke_editor']").attr('id');

                    //if the instance name starts with cke_ it was an auto-generated name from ckeditor
                    //that happens because in repeatable we stripe the field names used by ckeditor, so it renders a random name
                    //that starts with cke_
                    if($ck_instance_name.startsWith('cke_')) {
                        $ck_instance_name = $ck_instance_name.substr(4);
                    }
                    //we fully destroy the instance when element is deleted from the page.
                    CKEDITOR.instances[$ck_instance_name].destroy(true);
                });
                // trigger a new CKEditor
                element.ckeditor(element.data('options'));
            }
        </script>
    @endpush

@endif

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
