---
extends: _layouts.note
title: Adding (build-time) syntax highlighting to your Jigsaw site
date: 2025-06-20
section: content
---

<p class="lead">
    Adding syntax highlighting to your Jigsaw site’s code snippets is easy—just drop in a JS library and you’re good to go, right? Well… yes. But it’s not exactly the most performant approach.
</p>

## The problem

If you're using Jigsaw to build your site and regularly include code snippets, chances are you've either added syntax highlighting already or are planning to. The most straightforward solution is to drop in [highlight.js](https://highlightjs.org/) and let it do its magic on the client side. That’s what I did initially, too.

However, there's a catch: highlight.js isn’t exactly lightweight. Even with just the default language set, it adds around 18 KB to your page. On top of that, highlighting is done at runtime in the browser—which means large or numerous code blocks can noticeably impact performance and delay the page’s first meaningful paint.

But here's the thing: when we’re already precompiling our Markdown content into static HTML with Jigsaw—then why not handle syntax highlighting at build time as well?

In this article, I’ll walk you through how to add build-time syntax highlighting to fenced code blocks in your Jigsaw site. It’s faster, cleaner, and better for your users.

Take the following fenced code block as an example:

    ```php
    // baz.php
    
    <?php
    
    $foo = "bar";
    ```

This will let us turn the rendered HTML from being unstyled…

    // baz.php
    
    <?php
        
    $foo = "bar";

…to highlighted.

```php
// baz.php

<?php
    
$foo = "bar";
```

## Preparations

First, we need to install the package that will handle the actual syntax highlighting for us. There are several options out there that can do the job, but for this post (and my own site), I’ve chosen [highlight.php](https://github.com/scrivo/highlight.php) by Geert Bergman.

It’s a PHP port of the well-known [highlight.js](http://www.highlightjs.org/) by Ivan Sagalaev, offering familiar behavior in a server-side context—perfect for build-time rendering in a Jigsaw setup.

We’ll install the package via Composer:

```bash
composer require scrivo/highlight.php
```

Next, we need a place to put our custom syntax highlighting logic. Where exactly you place this code is up to you, but for the sake of this example, we’ll create a new `app/` directory at the root of the project.

To make sure everything in that folder is properly loaded, we’ll tell Composer to autoload it. Open your composer.json and add (or extend) the autoload section like this:

```json
// composer.json

{
    /* […] */

    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
}
```

Then run:

```bash
composer dump-autoload
```

This will ensure that any classes or helper files you place in `app/` are automatically available throughout your project.

To wrap up the setup, we need to provide Jigsaw with a bootstrap.php file placed in the root of the project.

If this file exists, Jigsaw will automatically load it before starting the build process. This gives us a convenient entry point to tweak internal behavior or hook into the build lifecycle—perfect for extending the default Markdown compilation with our custom syntax highlighting logic.

In a typical Jigsaw setup, this file already exists. If not, simply create it with the following contents:

```php
// bootstrap.php

<?php

/** @var $container \Illuminate\Container\Container */
/** @var $jigsaw \TightenCo\Jigsaw\Jigsaw */

```

## Extending the Markdown parser

To customize how fenced code blocks are handled during Markdown compilation, we’ll extend the default Markdown parser with a custom class. Jigsaw internally uses MarkdownExtra, and wraps it with its own `JigsawMarkdownParser`, which we’ll subclass.

Our custom parser lives at `app/Markdown/ExtendedMarkdownParser.php` and overrides the way fenced code blocks are processed during Markdown compilation.

Here's what happens in the class:

- In the constructor, we first call the parent constructor to inherit the default behavior.
- We then create an instance of `Highlight\Highlighter`, the PHP-based syntax highlighter we installed earlier.
- We explicitly define a list of languages (php, typescript, javascript, etc.) that the highlighter should try to auto-detect when no language is specified.
- We also set a `custom code_class_prefix`, which ensures the rendered HTML includes class names like language-javascript or language-php for applying custom CSS syntax themes.
- Most importantly, we assign a callback function to `code_block_content_func`. This function is called for every fenced code block during the build process. It receives the raw content of the code block and an optional language identifier.

The actual highlighting happens inside the private method `highlightFencedCodeBlock()`. This method:

1. Cleans up special placeholder syntax that might conflict with PHP tags (`<?php`). 
2. Tries to highlight the code using either the specified language or auto-detection. 
3. If highlighting fails (e.g., due to an unknown language), it gracefully falls back to returning the escaped code block as plain text.

Here’s the complete parser extension:

```php
// app/Markdown/ExtendedMarkdownParser.php

<?php

namespace App\Markdown;

use Highlight\Highlighter;
use Throwable;
use TightenCo\Jigsaw\Parsers\JigsawMarkdownParser;

class ExtendedMarkdownParser extends JigsawMarkdownParser
{
    private Highlighter $highlighter;

    private const AUTODETECT_LANGUAGES = [
        'php',
        'typescript',
        'javascript',
        'css',
        'json',
        'bash',
        'shell',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->highlighter = new Highlighter(true);
        $this->highlighter->setAutodetectLanguages(self::AUTODETECT_LANGUAGES);

        $this->code_class_prefix = 'hljs language-';

        $this->code_block_content_func = function (string $content, string $language): string {
            return $this->highlightFencedCodeBlock($content, $language);
        };
    }

    private function highlightFencedCodeBlock(
        string $content,
        string $language
    ): string {
        $content = strtr($content, ["<{{'?php'}}" => '<?php']);

        try {
            if(!empty($language)) {
                return $this->highlighter->highlight($language, $content)->value;
            } else {
                return $this->highlighter->highlightAuto($content)->value;
            }
        } catch (Throwable $e) {
            return htmlspecialchars($content, ENT_NOQUOTES);
        }
    }
}

```

## Wiring it all up

The final step is to tell Jigsaw to use our custom Markdown parser instead of the default one. We’ll do this by registering our `ExtendedMarkdownParser` in the `bootstrap.php` file we created earlier.

By binding our implementation to the `MarkdownParserContract` interface, Jigsaw will automatically use it during the build process:

```php
// bootstrap.php

<?php

use App\Markdown\ExtendedMarkdownParser;
use Illuminate\Container\Container;
use TightenCo\Jigsaw\Parsers\MarkdownParserContract;

/** @var Container $container */
$container->bind(MarkdownParserContract::class, ExtendedMarkdownParser::class);

```

And with that, you're all set—Jigsaw will now process your fenced code blocks using build-time syntax highlighting, no JavaScript required. All that’s left for you to do is include the appropriate CSS styles for the generated classes in your site’s stylesheet.

## Styles!

Now that syntax highlighting is in place, running the Jigsaw build process successfully transforms our fenced code blocks from the plain markup like:

```html
<pre><code class="language-php">&lt;?php

$foo = 'bar';</code></pre>
```

into a nicely highlighted version like this:

```html
<pre><code class="hljs language-php"><span class="hljs-meta">&lt;?php</span>

$foo = <span class="hljs-string">'bar'</span>;</code></pre>
```

Hooray! 🎉

Well—almost done. The final step is to actually style those fancy new classes.

Since the PHP highlighter we’re using is a direct port of highlight.js, we can simply use any of the official [highlight.js themes](https://github.com/isagalaev/highlight.js/tree/master/src/styles). You can preview them on [the project’s website](https://highlightjs.org/).

Just pick your favorite theme, extract the relevant CSS, and include it in your project. You can either import it directly or integrate the styles into your Tailwind setup for a more unified look.

Once that’s done, you’ve got fully integrated, fast, and lightweight syntax highlighting—no JavaScript needed.

## Last but not least, a concrete example

This very website uses the implementation outlined above, along with a slightly customized version of the dark theme from [a11y syntax highlighting](https://github.com/ericwbailey/a11y-syntax-highlighting) by Eric Bailey.

If you're curious about the actual setup, feel free to explore the code on GitHub:

-  Extended Markdown Parser: [app/Markdown/ExtendedMarkdownParser.php](#)
-  Bootstrap File: [bootstrap.php](#)
-  Syntax Highlighting Theme: [source/_assets/css/_syntax.css](#)

It’s all there—ready to be cloned, forked, or just browsed for inspiration.
