@extends('_layouts.main')

@section('body')

    <p class="mb-0">
        <date class="text-grey-light">{{ date('F jS, Y', $page->date) }}</date>
    </p>
    <h1>{{ $page->title }}</h1>

    @yield('content')

    @include('_partials/about')

@endsection
