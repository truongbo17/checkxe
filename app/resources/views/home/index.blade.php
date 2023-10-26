@extends('layouts.main')

@section('content')
    @include('home.search')
    @include('home.list', ['car_news' => $car_news])
    @livewire('contact-form')
@endsection
