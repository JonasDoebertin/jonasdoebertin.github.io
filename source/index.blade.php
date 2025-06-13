@extends('_layouts.main')

@section('body')

    <h1 class="sr-only">Website of Jonas Döbertin</h1>

    <p class="lead">
        Hi there! I’m <em>Jonas Döbertin</em>, a full stack <em>web developer</em> from <a href="https://www.google.de/maps/place/Hamburg/@53.5586941,9.7877393,11z/data=!4m5!3m4!1s0x47b161837e1813b9:0x4263df27bd63aa0!8m2!3d53.5510846!4d9.9936818">Hamburg,&nbsp;Germany</a> with a focus on <em>Shopware</em> and <em>Statamic</em>. As a development team lead at <a href="#">Digital Masters</a> together with our team I create modern websites and e-commerce platforms for our various clients.
    </p>

    <p>Get in touch at <a href="mailto:hello@jd-powered.net">hello@jd-powered.net</a>.</p>

    <h2>Latest Notes</h2>

    @foreach ($notes as $note)
        <p>
            <date>{{ date('F jS, Y', $note->date) }}</date><br>
            <a href="{{ $note->getPath() }}/">{{ $note->title }}</a>
        </p>
    @endforeach

@endsection
