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
        <meta name="apple-mobile-web-app-title" content="dj.dev" />

        @if($page->noindex)
            <meta name="robots" content="noindex, nofollow">
        @else
            <meta name="robots" content="index, follow">
        @endif

        <link rel="canonical" href="{{ $page->getUrl() }}">
        <link rel="home" href="{{ $page->baseUrl }}">

        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <link rel="manifest" href="/site.webmanifest" />

        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "Person",
                "name": "Jonas Döbertin",
                "jobTitle": "Development Team Lead",
                "skills": "Statamic, Laravel, Shopware, Kirby CMS, PHP, TypeScript, Tailwind CSS",
                "url": "{{ $page->baseUrl }}",
                "sameAs": [
                    "https://github.com/JonasDoebertin",
                    "https://www.threads.com/@dieserjonas",
                    "https://www.linkedin.com/in/jonas-d%C3%B6bertin/"
                ],
                "worksFor": {
                    "@type": "Organization",
                    "name": "Digital Masters",
                    "legalName": "Digital Masters GmbH",
                    "sameAs": "https://digital-masters.de/"
                }
            }
        </script>

        @if ($page->production)
            {{-- Insert tracking code here --}}
        @endif

        @viteRefresh()
        <link rel="stylesheet" href="{{ vite('source/_assets/css/main.css') }}">
        <script defer type="module" src="{{ vite('source/_assets/js/main.js') }}"></script>
    </head>
    <body class="min-h-(--min-h-frame) relative p-8 md:m-8 lg:px-24 lg:py-16 xl:px-48 xl:py-32 bg-white md:shadow-2xl text-zinc-950 font-sans subpixel-antialiased">

        @include('_partials.header')

        <main role="main" class="mb-16 xl:mb-32 prose md:prose-lg lg:prose-xl prose-zinc prose-a:text-brand">
            @yield('body')
        </main>

        <footer role="contentinfo">
            @include('_partials.social')
            @include('_partials.legal')
        </footer>

        @stack('scripts')
    </body>
</html>
