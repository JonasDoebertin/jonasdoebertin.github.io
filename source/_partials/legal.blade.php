<nav aria-label="Legal Information">
    <h3 class="sr-only">
        Legal Information
    </h3>

    <ul>
        @if ($page->getPath() !== '')
            <li class="block md:inline-block mb-4 md:mr-8">
                <a href="/" class="no-decoration" aria-label="Go back to the websites home page">
                    Back to home
                </a>
            </li>
        @endif

        <li class="block md:inline-block mb-4 md:mr-8">
            <a href="/imprint" class="no-decoration">Imprint</a>
        </li>
        <li class="block md:inline-block mb-4 md:mr-8">
            <a href="/disclaimer" class="no-decoration">Disclaimer</a>
        </li>
        <li class="block md:inline-block mb-4 md:mr-4">
            <a href="/privacy" class="no-decoration">Privacy & Cookie Notice</a>
        </li>
    </ul>
</nav>
