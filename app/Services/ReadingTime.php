<?php

namespace App\Services;

use App\Markdown\Nodes\Prompt;
use App\Markdown\Parsers\PromptParser;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Parser\MarkdownParser;

final class ReadingTime
{
    /**
     * Silent reading: Adults average 238 WPM for general text.
     * Reading aloud: Adults average 183 WPM.
     * Second language (English): Non-native readers often range between 100 and 200 WPM on familiar material depending on vocabulary knowledge.
     */
    private const int WORDS_PER_MINUTE = 183;

    private readonly MarkdownParser $parser;

    public function __construct()
    {
        $environment = new Environment;
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addInlineParser(new PromptParser, 100);

        $this->parser = new MarkdownParser($environment);
    }

    public function estimate(string $markdown): CarbonInterval
    {
        $minutes = $this->wordCount($markdown) / self::WORDS_PER_MINUTE;
        $minutes = max(1, ceil($minutes * 2) / 2);

        return CarbonInterval::seconds((int) round($minutes * 60))->cascade();
    }

    private function wordCount(string $markdown): int
    {
        return Str::wordCount($this->readableText($markdown));
    }

    private function readableText(string $markdown): string
    {
        $document = $this->parser->parse($markdown);
        $walker = $document->walker();
        $text = '';

        while ($event = $walker->next()) {
            if (! $event->isEntering()) {
                continue;
            }

            $node = $event->getNode();

            if ($node instanceof AbstractBlock) {
                $text .= "\n";
            }

            if ($node instanceof Text && ! $this->hasExcludedAncestor($node)) {
                $text .= $node->getLiteral();
            }
        }

        return $text;
    }

    private function hasExcludedAncestor(Node $node): bool
    {
        $ancestor = $node->parent();

        while ($ancestor !== null) {
            if (
                $ancestor instanceof Code
                || $ancestor instanceof FencedCode
                || $ancestor instanceof IndentedCode
                || $ancestor instanceof Image
                || $ancestor instanceof Prompt
            ) {
                return true;
            }

            $ancestor = $ancestor->parent();
        }

        return false;
    }
}
