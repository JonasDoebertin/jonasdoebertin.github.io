@extends('_layouts.main')

@section('body')

    <h1 class="sr-only">Website of Jonas Döbertin</h1>

    <p class="lead">
        Hi there! I’m <em class="font-medium">Jonas Döbertin</em>, a full-stack <em>web developer</em> based in <a href="https://www.google.de/maps/place/Hamburg/@53.5586941,9.7877393,11z/data=!4m5!3m4!1s0x47b161837e1813b9:0x4263df27bd63aa0!8m2!3d53.5510846!4d9.9936818" target="_blank" rel="noopener noreferrer">Hamburg,&nbsp;Germany</a>, with a focus on <em>Shopware</em> and <em>Statamic</em>. As development team lead at <a href="https://digital-masters.de/de" target="_blank" rel="noopener noreferrer">Digital Masters</a>,  I work with our team to build modern websites and e-commerce platforms for a wide range of clients.
    </p>

    <p>Get in touch at <a href="mailto:hello@dieserjonas.dev">hello@dieserjonas.dev</a>.</p>

    <h2>Latest Notes</h2>

    @foreach ($notes as $note)
        <p>
            <date>{{ date('F jS, Y', $note->date) }}</date><br>
            <a href="{{ $note->getPath() }}/">{{ $note->title }}</a>
        </p>
    @endforeach

    <h2>Current Projects</h2>

    @foreach ($projects->where('status', '!=', 'legacy') as $project)
        <article class="mb-16">
            <h3 class="!mt-0 no-decor">
                {{ $project->title }}
            </h3>

            {!! $project->getContent() !!}

            <nav class="" aria-label="Additional resources for {{ $project->title }}">
                <ul class="!mt-0 !ps-0">
                    @foreach ($project->links as $link)
                        <li class="inline-block !my-0 mr-8 !mb-2 !ps-0">
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['title'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </article>

    @endforeach

    <h2>Past Projects</h2>

    <div class="grid md:grid-cols-2 gap-x-16">
        @foreach ($projects->where('status', 'legacy') as $project)
            <article class="mb-16">
                <h3 class="!mt-0 no-decor">
                    {{ $project->title }}
                </h3>

                {!! $project->getContent() !!}

                <nav class="" aria-label="Additional resources for {{ $project->title }}">
                    <ul class="!mt-0 !ps-0">
                        @foreach ($project->links as $link)
                            <li class="inline-block !my-0 mr-8 !mb-2 !ps-0">
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </article>

        @endforeach
    </div>


@endsection
