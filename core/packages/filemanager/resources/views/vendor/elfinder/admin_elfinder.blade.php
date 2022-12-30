@extends(bo_view('layouts.top_left'))

@php
    $breadcrumbs = [
      trans('bo::crud.admin') => bo_url('dashboard'),
      trans('bo::logmanager.log_manager') => false,
    ];
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            {{ trans('bo::logmanager.log_manager') }}
            <small>{{ trans('bo::logmanager.log_manager_description') }}</small>
        </h2>
    </section>
@endsection

@section('content')
    <iframe src="{{route('elfinder.index')}}" title="W3Schools Free Online Web Tutorials"></iframe>
@endsection

@section('after_scripts')

@endsection
