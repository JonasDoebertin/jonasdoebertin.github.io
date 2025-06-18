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

        $this->code_class_prefix = 'highlighting language-';

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
