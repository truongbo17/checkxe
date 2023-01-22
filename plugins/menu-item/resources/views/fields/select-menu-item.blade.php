@php
    use Bo\PageManager\App\Models\Page;
    use Illuminate\Support\Collection;
    use Bo\Base\Library\CrudPanel\CrudPanelFacade;
@endphp
@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')


<?php
function tree_element($entry, $key, $all_entries, $crud)
{
    if (!isset($entry->tree_element_shown)) {
        // mark the element as shown
        $all_entries[$key]->tree_element_shown = true;
        $entry->tree_element_shown = true;

        if (!isset($entry->name)) {
            $entry->name = $entry->item_id;
        }

        // show the tree element
        $data_item = (string)json_encode($entry) ?? "";
        echo "<li id='list_{$entry->item_id}' data-item='$data_item'>";
        echo '<div class="d-flex"><span class="disclose"><span></span></span>' . $entry->name . '
        <button type="button" class="ml-auto btn btn-outline-secondary"><i class="las la-eye text-info"></i></button>
        <button type="button" class="pl-2 btn btn-outline-secondary"><i class="las la-pen-square text-warning"></i></button>
        <button type="button" class="pl-2 btn btn-outline-secondary"><i class="las la-trash-alt text-danger"></i></button>
        </div>';

        // see if this element has any children
        $children = [];
        foreach ($all_entries as $key => $subentry) {
            if ($subentry->parent_id == $entry->item_id) {
                $children[] = $subentry;
            }
        }

        $children = collect($children)->sortBy('lft');

        // if it does have children, show them
        if (count($children)) {
            echo '<ol>';
            foreach ($children as $key => $child) {
                $children[$key] = tree_element($child, $child->item_id, $all_entries, $crud);
            }
            echo '</ol>';
        }
        echo '</li>';
    }

    return $entry;
}

?>

<div class="row mt-4">
    <div class="col-md-12 col-md-offset-2">
        <div class="card p-4">
            <div class="d-flex">
                <p>{{ trans('bo::crud.reorder_text') }}</p>
                <button class="btn btn-sm btn-outline-primary ml-auto" type="button" data-toggle="modal"
                        data-target="#modal-add-menu-item"><i
                        class="las la-plus-circle"></i> {{trans('menu-item::menu-items.add-menu-item')}}</button>
            </div>

            <ol class="sortable mt-2">
                <?php
                Collection::macro('recursive', function () {
                    return $this->map(function ($value) {
                        if (is_array($value) || is_object($value)) {
                            return collect($value)->recursive();
                        }

                        return $value;
                    });
                });

                $items = json_decode($field['value'] ?? "", true) ?? [];
                $new_items = [];
                foreach ($items as $key => $value) {
                    if (isset($value['item_id'])) {
                        if (array_key_exists('tree_element_shown', $value)) {
                            unset($value['tree_element_shown']);
                        }
                        $new_items[$value['item_id']] = $value;
                    }
                }

                $all_entries = collect(json_decode(json_encode($new_items)));
                $root_entries = $all_entries->filter(function ($item) {
                    return $item->parent_id == 0;
                });
                foreach ($root_entries as $key => $entry) {
                    $root_entries[$key] = tree_element($entry, $key, $all_entries, $crud);
                }
                ?>
            </ol>
        </div><!-- /.card -->
    </div>
</div>

<div class="modal fade fade" id="modal-add-menu-item" tabindex="0" role="dialog" aria-labelledby="modal-add-menu-item"
     aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="category-inline-create-dialog-label">
                    {{trans('menu-item::menu-items.add-menu-item')}}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <div class="card">
                    @php
                        $options_type = [
                            'page_link' => 'Page Link',
                            'internal_link' => 'Internal Link',
                            'external_link' => 'External Link',
                            'router_name' => 'Router Name'
                        ];
                    @endphp
                    <div class="card-body row">
                        <div class="col-md-12">
                            <label for="menu_name">Menu name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Menu name"
                                name="menu_name"
                                id="menu_name">
                        </div>
                        <div class="form-group col-sm-12 mt-2">
                            <label for="type">Type <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-sm-3">
                                    <select name="type" id="type" class="form-control">
                                        @foreach($options_type as $key => $type)
                                            <option value="{{$key}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-9">
                                    <select name="page_link" id="page_link" class="form-control">
                                        @foreach(Page::where('deleted_at', null)->get() as $page)
                                            <option value="{{$page->id}}">{{$page->name}}</option>
                                        @endforeach
                                    </select>
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="{{ trans('bo::crud.internal_link_placeholder', ['url', url(config('bo.base.route_prefix').'/page')]) }}"
                                        name="internal_link"
                                        id="internal_link">
                                    <input
                                        type="url"
                                        class="form-control"
                                        placeholder="{{ trans('bo::crud.page_link_placeholder') }}"
                                        name="external_link"
                                        id="external_link"
                                    >
                                    <select name="router_name" id="router_name"
                                            style="width: 100%" class="form-control">
                                        @foreach(getRouteList() as $key => $router_name)
                                            <option value="{{$key}}">{{$router_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-add-menu-item">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
        <style>
            .ui-sortable .placeholder {
                outline: 1px dashed #4183C4;
                /*-webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                margin: -1px;*/
            }

            .ui-sortable .mjs-nestedSortable-error {
                background: #fbe3e4;
                border-color: transparent;
            }

            .ui-sortable ol {
                margin: 0;
                padding: 0;
                padding-left: 30px;
            }

            ol.sortable, ol.sortable ol {
                margin: 0 0 0 25px;
                padding: 0;
                list-style-type: none;
            }

            ol.sortable {
                margin: 2em 0;
            }

            .sortable li {
                margin: 5px 0 0 0;
                padding: 0;
            }

            .sortable li div {
                border: 1px solid #ddd;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                padding: 6px;
                margin: 0;
                cursor: move;
                background-color: #f4f4f4;
                color: #444;
                border-color: #00acd6;
            }

            .sortable li.mjs-nestedSortable-branch div {
                /*background-color: #00c0ef;*/
                /*border-color: #00acd6;*/
            }

            .sortable li.mjs-nestedSortable-leaf div {
                /*background-color: #00c0ef;*/
                border: 1px solid #ddd;
            }

            li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
                border-color: #999;
                background: #fafafa;
            }

            .ui-sortable .disclose {
                cursor: pointer;
                width: 10px;
                display: none;
            }

            .sortable li.mjs-nestedSortable-collapsed > ol {
                display: none;
            }

            .sortable li.mjs-nestedSortable-branch > div > .disclose {
                display: inline-block;
            }

            .sortable li.mjs-nestedSortable-collapsed > div > .disclose > span:before {
                content: '+ ';
            }

            .sortable li.mjs-nestedSortable-expanded > div > .disclose > span:before {
                content: '- ';
            }

            .ui-sortable h1 {
                font-size: 2em;
                margin-bottom: 0;
            }

            .ui-sortable h2 {
                font-size: 1.2em;
                font-weight: normal;
                font-style: italic;
                margin-top: .2em;
                margin-bottom: 1.5em;
            }

            .ui-sortable h3 {
                font-size: 1em;
                margin: 1em 0 .3em;;
            }

            .ui-sortable p, .ui-sortable ol, .ui-sortable ul, .ui-sortable pre, .ui-sortable form {
                margin-top: 0;
                margin-bottom: 1em;
            }

            .ui-sortable dl {
                margin: 0;
            }

            .ui-sortable dd {
                margin: 0;
                padding: 0 0 0 1.5em;
            }

            .ui-sortable code {
                background: #e5e5e5;
            }

            .ui-sortable input {
                vertical-align: text-bottom;
            }

            .ui-sortable .notice {
                color: #c33;
            }
        </style>
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script src="{{ asset('packages/jquery-ui-dist/jquery-ui.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('packages/nestedSortable/jquery.mjs.nestedSortable2.js') }}"
                type="text/javascript"></script>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                const modalMenuItem = $('#modal-add-menu-item').clone();
                $('#modal-add-menu-item').remove();
                $(document.body).append(modalMenuItem);

                resetMenuItem();

                const type = $('#type').val();
                $("#" + type).show();

                $('#type').on('change', function (e) {
                    resetMenuItem();
                    $("#" + e.target.value).show();
                });

                // initialize the nested sortable plugin
                $('.sortable').nestedSortable({
                    forcePlaceholderSize: true,
                    handle: 'div',
                    helper: 'clone',
                    items: 'li',
                    opacity: .6,
                    placeholder: 'placeholder',
                    revert: 250,
                    tabSize: 25,
                    tolerance: 'pointer',
                    toleranceElement: '> div',
                    maxLevels: {{ $crud->get('reorder.max_level') ?? 3 }},
                    isTree: true,
                    expandOnHover: 700,
                    startCollapsed: false
                });

                $('.disclose').on('click', function () {
                    $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
                });

                $('#save-add-menu-item').click(function (e) {
                    $('input[name="menu_name"]').removeClass('is-invalid');
                    $('label[for="menu_name"]').removeClass('text-danger');
                    $('#type').removeClass('is-invalid');
                    $('label[for="type"]').removeClass('text-danger');

                    let validateCheck = false;

                    const nameValue = $('input[name="menu_name"]').val();
                    if (!nameValue) {
                        $('input[name="menu_name"]').addClass('is-invalid');
                        $('label[for="menu_name"]').addClass('text-danger');
                        validateCheck = true;
                    }

                    const type = $('#type').val();
                    const value = $("#" + type).val();
                    $("#" + type).parent().find('.is-invalid').removeClass('is-invalid');
                    if (!value) {
                        $('#type').addClass('is-invalid');
                        $('label[for="type"]').addClass('text-danger');
                        $("#" + type).addClass('is-invalid');
                        validateCheck = true;
                    }

                    if (!validateCheck) {
                        //Lấy ra key lớn nhất của list item
                        const arrayed = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
                        let bigKey = 0;
                        for (const key in arrayed) {
                            let item_id = arrayed[key].item_id;
                            if (item_id) {
                                item_id = item_id.replace('list_', '');
                                if (Number(bigKey) < Number(item_id)) {
                                    bigKey = Number(item_id);
                                }
                            }
                        }

                        //Append html vào list
                        $('.sortable').append(`
                            <li id="list_${bigKey + 1}" class="mjs-nestedSortable-leaf">
                                <div class="ui-sortable-handle">
                                    <span class="disclose"><span></span></span>${$('input[name="menu_name"]').val()}
                                </div>
                            </li>`);
                        $('.sortable').sortable('refresh');

                        //Sau khi append vào thì lấy cái item vừa được add vào
                        const arrayed_second = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
                        let array_item = arrayed_second.pop();
                        array_item['name'] = nameValue;
                        array_item['type'] = type;
                        array_item['value'] = value;
                        $(`li#list_${bigKey + 1}`).attr('data-item', JSON.stringify(array_item));

                        // Reset lại form
                        $('input[name="menu_name"]').val('');
                        $('#type').val('page_link');
                        resetMenuItem();
                        $('#page_link').show();
                        $('#modal-add-menu-item').find('button').first().click();
                    }
                });

                $("form").submit(function (e) {
                    const arrayed = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
                    for (let key = 0; key < arrayed.length; key++) {
                        if (arrayed[key].item_id == null) {
                            arrayed.splice(key, 1);
                        }
                        if ($('li#list_' + arrayed[key].item_id).attr("data-item") != undefined) {
                            const dataFromDom = JSON.parse($('li#list_' + arrayed[key].item_id).attr("data-item"));
                            arrayed[key] = {
                                ...dataFromDom,
                                ...arrayed[key],
                            };
                        } else {
                            arrayed[key]['name'] = 'Menu item ' + arrayed[key].item_id;
                            arrayed[key]['type'] = null;
                            arrayed[key]['value'] = null;
                        }
                    }

                    const input = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "item").val(JSON.stringify(arrayed));
                    $('form').append(input);
                    return true;
                });
            });

            function resetMenuItem() {
                $('#page_link').hide();
                $('#internal_link').hide();
                $('#external_link').hide();
                $('#router_name').hide();
            }
        </script>
    @endpush

@endif

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
