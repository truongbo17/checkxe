
<?php
$field['allows_null'] = $field['allows_null'] ?? false;

$field['name']['type'] = $field['name']['type'] ?? $field['name'][0] ?? 'type';
$field['name']['link'] = $field['name']['link'] ?? $field['name'][1] ?? 'link';
$field['name']['page_id'] = $field['name']['page_id'] ?? $field['name'][2] ?? 'page_id';
$field['name']['router_name'] = $field['name']['router_name'] ?? $field['name'][3] ?? 'router_name';

$field['options']['page_link'] = $field['options']['page_link'] ?? trans('bo::crud.page_link');
$field['options']['internal_link'] = $field['options']['internal_link'] ?? trans('bo::crud.internal_link');
$field['options']['external_link'] = $field['options']['external_link'] ?? trans('bo::crud.external_link');
$field['options']['router_name'] = $field['options']['router_name'] ?? trans('bo::base.router_name');

$field['pages'] = $field['pages'] ?? ($field['page_model'] ?? config('bo.pagemanager.page_model_class'))::all();

$field['router_name'] = $field['router_name'] ?? getRouteListAdmin();
?>

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('crud_fields_styles')
    <!-- include select2 css-->
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet"
          type="text/css"/>
@endpush

<div class="row" data-init-function="bpFieldInitPageOrLinkElement">
    {{-- hidden placeholders for content --}}
    <input type="hidden" value=""
           name="{{ $field['name']['page_id'] }}"/>
    <input type="hidden" value=""/>

    <input type="hidden" value=""
           name="{{ $field['name']['router_name'] }}"/>

    <div class="col-sm-3">
        {{-- type select --}}
        <select
            data-identifier="page_or_link_select"
            name="{!! $field['name']['type'] !!}"
            @include('crud::fields.inc.attributes')
        >

            @if ($field['allows_null'])
                <option value="">-</option>
            @endif

            @foreach ($field['options'] as $key => $value)
                <option value="{{ $key }}"
                >{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-9">
        {{-- page slug input --}}
        <div
            class="page_or_link_value page_link">
            <select
                class="form-control"
                for="{{ $field['name']['page_id'] }}"
                required
            >
                @foreach ($field['pages'] as $key => $page)
                    <option value="{{ $page->id }}"
                    >{{ $page->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- internal link input --}}
        <div
            class="page_or_link_value internal_link d-none">
            <input
                type="text"
                class="form-control"
                placeholder="{{ trans('bo::crud.internal_link_placeholder', ['url', url(config('bo.base.route_prefix').'/page')]) }}"
                for="{{ $field['name']['link'] }}"

{{--                @if (isset($entry) && $entry->{$field['name']['type']} !== 'internal_link')--}}
{{--                    disabled="disabled"--}}
{{--                @endif--}}

{{--                @if (isset($entry) && $entry->{$field['name']['type']} === 'internal_link' && $entry->{$field['name']['link']})--}}
{{--                    value="{{ $entry->{$field['name']['link']} }}"--}}
{{--                @endif--}}
            >
        </div>

        {{-- external link input --}}
        <div
            class="page_or_link_value external_link d-none">
            <input
                type="url"
                class="form-control"
                placeholder="{{ trans('bo::crud.page_link_placeholder') }}"
                for="{{ $field['name']['link'] }}"

{{--                @if (isset($entry) && $entry->{$field['name']['type']} !== 'external_link')--}}
{{--                    disabled="disabled"--}}
{{--                @endif--}}

{{--                @if (isset($entry) && $entry->{$field['name']['type']} === 'external_link' && $entry->{$field['name']['link']})--}}
{{--                    value="{{ $entry->{$field['name']['link']} }}"--}}
{{--                @endif--}}
            >
        </div>

        {{-- router name input --}}
        <div
            class="page_or_link_value router_name d-none">
            <select
                class="form-control"
                form="{{ $field['name']['router_name'] }}"
                required
            >
                @foreach ($field['router_name'] as $key => $router_name)
                    <option value="{{$key}}"
{{--                            @if (isset($entry) && $key === $entry->{$field['name']['router_name']})--}}
{{--                                selected--}}
{{--                        @endif--}}
                    >{{$router_name}}</option>
                @endforeach
            </select>
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

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- include select2 js-->
        <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
        @if (app()->getLocale() !== 'en')
            <script
                src="{{ asset('packages/select2/dist/js/i18n/' . str_replace('_', '-', app()->getLocale()) . '.js') }}"></script>
        @endif
        <script>
            $(document).ready(function () {
                $('.page_or_link_value select').select2({
                    theme: "bootstrap",
                });
            });

            if ($('input[name={{ $field['name']['page_id'] }}]').val() === "") {
                $('input[name={{ $field['name']['page_id'] }}]').val($('.page_link select').find("option:first-child").val());
            }

            function bpFieldInitPageOrLinkElement(element) {
                element = element[0]; // jQuery > Vanilla

                const select = element.querySelector('select[data-identifier=page_or_link_select]');
                const values = element.querySelectorAll('.page_or_link_value');

                // updates hidden fields
                const updateHidden = () => {
                    let selectedInput = select.value && element.querySelector(`.${select.value}`).firstElementChild;
                    element.querySelectorAll(`input[type="hidden"]`).forEach(hidden => {
                        hidden.value = selectedInput && hidden.getAttribute('name') === selectedInput.getAttribute('for') ? selectedInput.value : '';
                    });
                }

                // save input changes to hidden placeholders
                // values.forEach(value => value.firstElementChild.addEventListener('input', updateHidden));
                values.forEach(function (value) {
                    if ($(value.firstElementChild).get(0).tagName.toLowerCase() === 'select') {
                        $(value.firstElementChild).on('select2:select', updateHidden)
                    } else {
                        value.firstElementChild.addEventListener('input', updateHidden)
                    }
                });

                // main select change
                select.addEventListener('change', () => {
                    values.forEach(value => {
                        let isSelected = value.classList.contains(select.value);

                        // toggle visibility and disabled
                        value.classList.toggle('d-none', !isSelected);
                        value.firstElementChild.toggleAttribute('disabled', !isSelected);
                    });

                    // updates hidden fields
                    updateHidden();
                });
            }
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
