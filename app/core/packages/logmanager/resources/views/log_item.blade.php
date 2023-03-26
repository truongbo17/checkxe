@extends(bo_view('layouts.top_left'))

@php
  $breadcrumbs = [
    trans('bo::crud.admin') => bo_url('dashboard'),
    trans('logmanager::logmanager.log_manager') => bo_url('log'),
    trans('logmanager::logmanager.preview') => false,
  ];
@endphp

@section('header')
    <section class="container-fluid">
      <h2>
        {{ trans('logmanager::logmanager.log_manager') }}<small>{{ trans('logmanager::logmanager.file_name') }}: <i>{{ $file_name }}</i></small>
        <small><a href="{{ bo_url('log') }}" class="hidden-print font-sm"><i class="la la-angle-double-left"></i> {{ trans('logmanager::logmanager.back_to_all_logs') }}</a></small>
      </h2>
    </section>
@endsection

@section('content')
  <div id="accordion" role="tablist" aria-multiselectable="true">
    @forelse($logs as $key => $log)
      <div class="card mb-0 pb-0">
        <div class="card-header bg-{{ $log['level_class'] }}" role="tab" id="heading{{ $key }}">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $key }}" aria-expanded="true" aria-controls="collapse{{ $key }}" class="text-white">
              <i class="la la-{{ $log['level_img'] }}"></i>
              <span>[{{ $log['date'] }}]</span>
              {{ Str::limit($log['text'], 150) }}
            </a>
        </div>
        <div id="collapse{{ $key }}" class="panel-collapse collapse p-3" role="tabpanel" aria-labelledby="heading{{ $key }}">
          <div class="panel-body">
            <p>{{$log['text']}}</p>
            <pre><code class="php">{{ trim($log['stack']) }}</code></pre>
          </div>
        </div>
      </div>
    @empty
      <h3 class="text-center">No Logs to display.</h3>
    @endforelse
  </div>

@endsection

@section('after_scripts')
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/default.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js"></script>
  <script>hljs.initHighlightingOnLoad();</script>
@endsection
