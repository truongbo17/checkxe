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
            <h1>{{ trans('pluginmanager::pluginmanager.name') }}</h1>
        </div>
    </section>
@endsection

@section('content')

@endsection
