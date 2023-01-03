@extends(bo_view('blank'))

@section('after_styles')
@endsection

@php
    $breadcrumbs = [
        trans('bo::crud.admin') => url(config('bo.base.route_prefix'), 'dashboard'),
        trans('pluginmanager::pluginmanager.name') => false,
    ];
@endphp

@section('header')
    <section class="content-header">
        <div class="container-fluid mb-3">
            <h2>{{ trans('pluginmanager::pluginmanager.name') }}
                <small>{{ trans('pluginmanager::pluginmanager.description') }}</small></h2>
        </div>
    </section>
@endsection

@section('content')
    <div class="row row-cols-3 g-4">
        @foreach($plugins as $plugin)
            <div class="col-md-4">
                <div class="card">
                    <img
                        src="data:image/png;base64,{{$plugin['image']}}"
                        class="card-img-top w-100" style="height: 160px" alt="{{$plugin['name']}}">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title">{{$plugin['name']}}</h5>
                            <span class="ml-1 badge badge-danger"
                                  style="margin-bottom: auto">{{$plugin['version']}}</span>
                        </div>
                        <p class="card-text"><strong>{{$plugin['author']}}</strong></p>
                        <p class="card-text"><small>{{$plugin['description']}}</small></p>
                        <div class="d-flex justify-content-around">
                            @if($plugin['active'])
                                <button
                                    class="w-50 btn btn-warning"
                                    onclick="deactivatePlugin('{{ $plugin['path'] }}')">{{ trans('pluginmanager::pluginmanager.deactivate') }}</button>
                            @else
                                <button
                                    class="w-50 btn btn-success"
                                    onclick="activePlugin('{{ $plugin['path'] }}')">{{ trans('pluginmanager::pluginmanager.active') }}</button>
                            @endif
                            <button class="btn btn-danger"
                                    onclick="removePlugin('{{ $plugin['path'] }}')">{{ trans('pluginmanager::pluginmanager.remove') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('after_scripts')
    <script>
        function activePlugin(pluginPath) {
            $.post("{{route("plugin.activate")}}",
                {
                    pluginPath: pluginPath,
                },
                function () {
                    location.reload();
                });
        }

        function deactivatePlugin(pluginPath) {
            $.post("{{route("plugin.deactivate")}}",
                {
                    pluginPath: pluginPath,
                },
                function () {
                    location.reload();
                });
        }

        function removePlugin(pluginPath) {
            $.post("{{route("plugin.remove")}}",
                {
                    pluginPath: pluginPath,
                },
                function () {
                    location.reload();
                });
        }
    </script>
@endpush
