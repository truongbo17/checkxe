@extends('layouts.main')

@section('content')
    @include('home.search')
    @include('home.list')
    @livewire('contact-form')
@endsection
