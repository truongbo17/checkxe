<!-- select2 multiple -->

@if(isset($entry) && $entry->is_admin !== \Bo\PermissionManager\App\Enum\IsAdminEnum::IS_ADMIN)
    @include('crud::fields.inc.wrapper_start')
    <label class="text-error">User don't user admin</label>
    @include('crud::fields.inc.translatable_icon')
    <div class="text-center">
        <a href="javascript:void(0)"
           onclick="updateEntry(this)"
           data-route="{{ url($crud->route.'/'.$entry->getKey().'/update_status_admin') }}"
           class="btn btn-primary"
           data-button-type="patch"
        ><i class="las la-{{$entry->is_admin == \Bo\PermissionManager\App\Enum\IsAdminEnum::IS_ADMIN ? 'user-minus' : 'user-plus'}}"></i> {{$entry->is_admin == \Bo\PermissionManager\App\Enum\IsAdminEnum::IS_ADMIN ? 'Remove admin' : 'Add admin for this user'}}
        </a>
    </div>
    @include('crud::fields.inc.wrapper_end')
    @if ($crud->fieldTypeNotLoaded($field))
        @php
            $crud->markFieldTypeAsLoaded($field);
        @endphp
        @push('crud_fields_scripts')
            <script>
                if (typeof updateEntry != "function") {
                    $("[data-button-type=patch]").unbind("click");

                    function updateEntry(button) {
                        var route = $(button).attr("data-route");

                        swal({
                            title: "Do you change permission admin for this user ?",
                            icon: "info",
                            buttons: ["Cancel", "Yes"],
                            dangerMode: true,
                        }).then((value) => {
                            if (value) {
                                $.ajax({
                                    url: route,
                                    type: "post",
                                    success: function (result) {
                                        if (result == 1) {
                                            if (
                                                typeof crud != "undefined" &&
                                                typeof crud.table != "undefined"
                                            ) {
                                                // Move to previous page in case of deleting the only item in table
                                                if (crud.table.rows().count() === 1) {
                                                    crud.table.page("previous");
                                                }

                                                crud.table.draw(false);
                                            }

                                            // Show a success notification bubble
                                            new Noty({
                                                type: "success",
                                                text: "Updated status admin user",
                                            }).show();

                                            // Hide the modal, if any
                                            $(".modal").modal("hide");

                                            //reload page
                                            location.reload(true);
                                        } else {
                                            // if the result is an array, it means
                                            // we have notification bubbles to show
                                            if (result instanceof Object) {
                                                // trigger one or more bubble notifications
                                                Object.entries(result).forEach(function (
                                                    entry,
                                                    index
                                                ) {
                                                    var type = entry[0];
                                                    entry[1].forEach(function (message, i) {
                                                        new Noty({
                                                            type: "Success",
                                                            text: "Updated status admin user",
                                                        }).show();
                                                    });
                                                    //reload page
                                                    location.reload(true);
                                                });
                                            } else {
                                                // Show an error alert
                                                swal({
                                                    title: "Failure",
                                                    icon: "error",
                                                    timer: 4000,
                                                    buttons: false,
                                                });
                                            }
                                        }
                                    },
                                    error: function (result) {
                                        // Show an alert with the result
                                        swal({
                                            title: "Failure",
                                            icon: "error",
                                            timer: 4000,
                                            buttons: false,
                                        });
                                    },
                                });
                            }
                        });
                    }
                }

                // make it so that the function above is run after each DataTable draw event
                // crud.addFunctionToDataTablesDrawEventQueue('updateEntry');
            </script>
        @endpush
    @endif
@else
    @php
        if (!isset($field['options'])) {
            $field['options'] = $field['model']::all();
        } else {
            $field['options'] = call_user_func($field['options'], $field['model']::query());
        }

        //build option keys array to use with Select All in javascript.
        $model_instance = new $field['model'];
        $options_ids_array = $field['options']->pluck($model_instance->getKeyName())->toArray();

        $field['multiple'] = $field['multiple'] ?? true;
        $field['allows_null'] = $field['allows_null'] ?? $crud->model::isColumnNullable($field['name']);
    @endphp

    @include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')
    <select
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        data-init-function="bpFieldInitSelect2MultipleElement"
        data-field-is-inline="{{var_export($inlineCreate ?? false)}}"
        data-select-all="{{ var_export($field['select_all'] ?? false)}}"
        data-options-for-js="{{json_encode(array_values($options_ids_array))}}"
        data-language="{{ str_replace('_', '-', app()->getLocale()) }}"
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control select2_multiple'])
        {{ $field['multiple'] ? 'multiple' : '' }}>

        @if (isset($field['model']))
            @foreach ($field['options'] as $option)
                @if( (old(square_brackets_to_dots($field["name"])) && in_array($option->getKey(), old($field["name"]))) || (is_null(old(square_brackets_to_dots($field["name"]))) && isset($field['value']) && in_array($option->getKey(), $field['value']->pluck($option->getKeyName(), $option->getKeyName())->toArray())))
                    <option value="{{ $option->getKey() }}" data-option="{{json_encode($option->list_route_admin)}}"
                            selected>{{ $option->{$field['attribute']} }}</option>
                @else
                    <option value="{{ $option->getKey() }}"
                            data-option="{{json_encode($option->list_route_admin)}}">{{ $option->{$field['attribute']} }}</option>
                @endif
            @endforeach
        @endif
    </select>

    @if(isset($field['select_all']) && $field['select_all'])
        <a class="btn btn-xs btn-default select_all" style="margin-top: 5px;"><i
                class="la la-check-square-o"></i> {{ trans('bo::crud.select_all') }}</a>
        <a class="btn btn-xs btn-default clear" style="margin-top: 5px;"><i
                class="la la-times"></i> {{ trans('bo::crud.clear') }}</a>
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
    @include('crud::fields.inc.wrapper_end')

    <div class="form-group col-sm-12" id="detail-row">
        <label for="detail-row">Detail permission : </label>
        <div>
            <table id="crudTable"
                   class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed has-hidden-columns"
                   data-responsive-table="1">
                <thead>
                <tr>
                    <th>Route name</th>
                    <th>Route link</th>
                    <th>Route function</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <th>Route name</th>
                    <th>Route link</th>
                    <th>Route function</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>


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
                function bpFieldInitSelect2MultipleElement(element) {

                    var $select_all = element.attr('data-select-all');
                    if (!element.hasClass("select2-hidden-accessible")) {
                        let $isFieldInline = element.data('field-is-inline');

                        var $obj = element.select2({
                            theme: "bootstrap",
                            dropdownParent: $isFieldInline ? $('#inline-create-dialog .modal-content') : document.body
                        });

                        //get options ids stored in the field.
                        var options = JSON.parse(element.attr('data-options-for-js'));

                        if ($select_all) {
                            element.parent().find('.clear').on("click", function () {
                                $obj.val([]).trigger("change");
                            });
                            element.parent().find('.select_all').on("click", function () {
                                $obj.val(options).trigger("change");
                            });
                        }
                    }
                }

                $(document).ready(function () {
                    $('#detail-row').hide();

                    let arrayFlattenSelected = [];
                    const optionSelectEdit = $("option:selected");
                    const arrayValueSelected = [];
                    // Get array list permission role selected
                    optionSelectEdit.each(function (i, option) {
                        const dataOption = $(option).attr('data-option');
                        if (typeof dataOption === 'string') {
                            const detailRole = JSON.parse(dataOption);
                            if (Array.isArray(detailRole)) {
                                arrayValueSelected.push(detailRole);
                            }
                        }
                    });
                    arrayFlattenSelected = arrayFlatten(arrayValueSelected)
                    showDetailRoleString(removeDuplicateArray(arrayFlattenSelected));

                    //show detail value
                    function showDetailRoleString(detailRole) {
                        if (detailRole.length < 1) {
                            $('#detail-row').hide();
                            $('select').find('option:selected').remove();
                        } else {
                            let stringBodyTableDetail = '';
                            detailRole.forEach(function (value) {
                                stringBodyTableDetail +=
                                    `<tr>
                                <td class="dtr-control">
                                    <span>${value.route_name}</span>
                                </td>
                                <td class="dtr-control">
                                    <span><a href="${value.route_link_input}">${value.route_link_input}</a></span>
                                </td>
                                <td class="dtr-control">
                                    <span>${value.route_function}</span>
                                </td>
                            </tr>`;
                            });
                            $('#detail-row tbody').html(stringBodyTableDetail);
                            $('#detail-row').show();
                        }
                    }

                    // Remove duplicate route permission
                    function removeDuplicateArray(arrayRole) {
                        let arrayRemoveDuplicate = [];
                        arrayRole.forEach(function (value) {
                            if (!arrayRemoveDuplicate.some(role => role.route_name === value.route_name && role.route_link === value.route_link && role.route_function === value.route_function && role.route_link_input === value.route_link_input)) {
                                arrayRemoveDuplicate.push(value);
                            }
                        });
                        return arrayRemoveDuplicate;
                    }

                    // Array flatten selected
                    function arrayFlatten(array) {
                        if (Array.isArray(array)) {
                            return [].concat.apply([], array);
                        }
                        return false;
                    }

                    $(document.body).on("change", "select", function (e) {
                        const optionSelectedArray = $("option:selected", this);
                        arrayFlattenSelected = [];
                        for (let i = 0; i < optionSelectedArray.length; i++) {
                            let optionSelected = optionSelectedArray[i];
                            const dataOption = $(optionSelected).attr('data-option');
                            if (typeof dataOption === 'string') {
                                const detailRole = JSON.parse(dataOption);
                                if (Array.isArray(detailRole)) {
                                    detailRole.forEach(function (value) {
                                        arrayFlattenSelected.push(value);
                                    });
                                }
                            }
                        }
                        showDetailRoleString(removeDuplicateArray(arrayFlattenSelected));
                    });
                });
            </script>
        @endpush

    @endif
    {{-- End of Extra CSS and JS --}}
    {{-- ########################################## --}}
@endif
