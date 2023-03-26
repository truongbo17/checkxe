@extends(bo_view('layouts.top_left'))

@php
  $defaultBreadcrumbs = [
    trans('bo::crud.admin') => bo_url('dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('bo::crud.edit').' '.trans('langfilemanager::langfilemanager.texts') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
	<section class="container-fluid">
	  <h2>
        <span class="text-capitalize">{{ trans('langfilemanager::langfilemanager.translate') }}</span>
        <small>{{ trans('langfilemanager::langfilemanager.site_texts') }}.</small>

        @if ($crud->hasAccess('list'))
          <small><a href="{{ url($crud->route) }}" class="hidden-print font-sm"><i class="fa fa-angle-double-left"></i> {{ trans('bo::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
        @endif
	  </h2>
	</section>
@endsection

@section('content')
<!-- Default box -->
  <div class="box">
  	<div class="box-header with-border">
	  <h3 class="box-title float-right pr-1">
		<small>
			 &nbsp; {{ trans('langfilemanager::langfilemanager.switch_to') }}: &nbsp;
			<select name="language_switch" id="language_switch">
				@foreach ($languages as $lang)
				<option value="{{ url(config('bo.base.route_prefix', 'admin')."/language/texts/{$lang->abbr}") }}" {{ $currentLang == $lang->abbr ? 'selected' : ''}}>{{ $lang->name }}</option>
				@endforeach
			</select>
		</small>
	  </h3>
	</div>
    <div class="box-body">
		<ul class="nav nav-tabs">
			@foreach ($langFiles as $file)
			<li class="nav-item">
				<a class="nav-link {{ $file['active'] ? 'active' : '' }}" href="{{ $file['url'] }}">{{ $file['name'] }}</a>
			</li>
			@endforeach
		</ul>
		<section class="tab-content p-3 lang-inputs">
		@if (!empty($fileArray))
			<form
				method="post"
				id="lang-form"
				class="form-horizontal"
				data-required="{{ trans('admin.language.fields_required') }}"
		  		action="{{ url(config('bo.base.route_prefix', 'admin')."/language/texts/{$currentLang}/{$currentFile}") }}"
		  		>
				{!! csrf_field() !!}
				<div class="form-group row">
					<div class="col-sm-2">
						<h5>{{ trans('langfilemanager::langfilemanager.key') }}</h5>
					</div>
					<div class="hidden-sm hidden-xs col-md-5">
						<h5>{{ trans('langfilemanager::langfilemanager.language_text', ['language_name' => $browsingLangObj->name]) }}</h5>
					</div>
					<div class="col-sm-10 col-md-5">
						<h5>{{ trans('langfilemanager::langfilemanager.language_translation', ['language_name' => $currentLangObj->name]) }}</h5>
					</div>
				</div>
				{!! $langfile->displayInputs($fileArray) !!}
				<hr>
				<div class="text-center">
					<button type="submit" class="btn btn-success submit">{{ trans('bo::crud.save') }}</button>
				</div>
				</form>
				@else
					<em>{{ trans('langfilemanager::langfilemanager.empty_file') }}</em>
				@endif
			</section>
    </div><!-- /.card-body -->
    	<p><small>{!! trans('langfilemanager::langfilemanager.rules_text') !!}</small></p>
  </div><!-- /.card -->
@endsection

@section('after_scripts')
	<script>
		jQuery(document).ready(function($) {
			$("#language_switch").change(function() {
				window.location.href = $(this).val();
			})
		});
	</script>
@endsection
