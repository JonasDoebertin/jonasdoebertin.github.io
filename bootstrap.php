<?php

use App\Listeners\GenerateSitemap;
use App\Markdown\ExtendedMarkdownParser;
use Illuminate\Container\Container;
use TightenCo\Jigsaw\Events\EventBus;
use TightenCo\Jigsaw\Parsers\MarkdownParserContract;

/** @var Container $container */
$container->bind(MarkdownParserContract::class, ExtendedMarkdownParser::class);

/** @var EventBus $events */
$events->afterBuild(GenerateSitemap::class);

/*
 * You can run custom code at different stages of the build process by
 * listening to the 'beforeBuild', 'afterCollections', and 'afterBuild' events.
 *
 * For example:
 *
 * $events->beforeBuild(function (Jigsaw $jigsaw) {
 *     // Your code here
 * });
 */
