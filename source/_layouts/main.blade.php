<!DOCTYPE html>
<html lang="{{ $page->language ?? 'en' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>{{ $page->title ?  $page->title . ' | ' : '' }}{{ $page->siteName }}</title>
        <meta name="description" content="{{ $page->description ?? $page->siteDescription }}">
        <meta property="og:title" content="{{ $page->title ? $page->title . ' | ' : '' }}{{ $page->siteName }}"/>
        <meta property="og:type" content="{{ $page->type ?? 'website' }}" />
        <meta property="og:url" content="{{ $page->getUrl() }}"/>
        <meta property="og:description" content="{{ $page->description ?? $page->siteDescription }}" />

        <link rel="canonical" href="{{ $page->getUrl() }}">
        <link rel="home" href="{{ $page->baseUrl }}">
        <link rel="icon" href="/favicon.ico">

        @if ($page->production)
            {{-- Insert tracking code here --}}
        @endif

        @viteRefresh()
        <link rel="stylesheet" href="{{ vite('source/_assets/css/main.css') }}">
        <script defer type="module" src="{{ vite('source/_assets/js/main.js') }}"></script>
    </head>
    <body class="min-h-(--min-h-frame) relative p-8 md:m-8 lg:px-24 lg:py-16 xl:px-48 xl:py-32 bg-white md:shadow-2xl text-zinc-950 font-sans subpixel-antialiased">

        @include('_partials.header')

        <main role="main" class="mb-8 lg:mb-16 xl:mb-32 prose md:prose-lg lg:prose-xl prose-zinc prose-a:text-brand">
            @yield('body')
        </main>

        <footer role="contentinfo">
            @include('_partials.social')
            @include('_partials.legal')
        </footer>

        @stack('scripts')
    </body>
</html>
