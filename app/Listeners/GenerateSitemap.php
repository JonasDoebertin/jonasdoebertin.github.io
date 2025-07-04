<?php

declare(strict_types=1);

namespace App\Listeners;

use samdark\sitemap\Sitemap;
use TightenCo\Jigsaw\Jigsaw;

class GenerateSitemap
{
    private const array EXCLUDE = [
        '/apple-touch-icon.png',
        '/assets/*',
        '/favicon*',
        '/humans.txt',
        '/images/*',
        '/robots.txt',
        '/site.webmanifest',
        '/web-app-manifest-*',
        '*/404*',
    ];

    public function handle(Jigsaw $jigsaw): void
    {
        $baseUrl = $jigsaw->getConfig('baseUrl');

        $sitemap = new Sitemap($jigsaw->getDestinationPath() . '/sitemap.xml');

        collect($jigsaw->getOutputPaths())
            ->reject(
                fn (string $path): bool => $this->isExcluded($path)
            )
            ->each(
                function (string $path) use ($baseUrl, $sitemap): void {
                    $uri = rtrim((string) $baseUrl, '/') . $path;
                    $sitemap->addItem($uri, time(), Sitemap::DAILY);
                }
            );

        $sitemap->write();
    }

    public function isExcluded($path): bool
    {
        return str($path)->is(self::EXCLUDE, true);
    }
}
