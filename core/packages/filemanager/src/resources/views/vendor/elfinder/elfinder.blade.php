@extends('bo::layouts.top_left')


@section('after_scripts')
        @include('elfinder::common_scripts')
        @include('elfinder::common_styles')

        <!-- elFinder initialization (REQUIRED) -->
        <script type="text/javascript" charset="utf-8">
            // Documentation for client options:
            // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
            $().ready(function() {
                $('#elfinder').elfinder({
                    // set your elFinder options here
                    @if($locale)
                        lang: '{{ $locale }}', // locale
                    @endif
                    customData: {
                        _token: '{{ csrf_token() }}'
                    },
                    url : '{{ route("elfinder.connector") }}',  // connector URL
                    soundPath: '{{ asset($dir.'/sounds') }}'
                });
            });
        </script>
@endsection

@php
  $breadcrumbs = [
    trans('bo::crud.admin') => url(config('bo.base.route_prefix'), 'dashboard'),
    trans('bo::crud.file_manager') => false,
  ];
@endphp

@section('header')
    <section class="container-fluid">
      <h2>{{ trans('bo::crud.file_manager') }}</h2>
    </section>
@endsection

@section('content')

        <!-- Element where elFinder will be created (REQUIRED) -->
        <div id="elfinder"></div>

@endsection
