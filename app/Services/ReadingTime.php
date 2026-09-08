<?php

namespace App\Services;

use App\Markdown\Nodes\Prompt;
use App\Markdown\Parsers\PromptParser;
use Carbon\CarbonInterval;
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
     * Average adult silent reading rate for English non-fiction.
     *
     * Marc Brysbaert, "How many words do we read per minute? A review and
     * meta-analysis of reading rate", Journal of Memory and Language 109 (2019).
     * https://doi.org/10.1016/j.jml.2019.104047
     */
    private const int WORDS_PER_MINUTE = 238;

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
        $count = preg_match_all(
            "/[\\p{L}\\p{N}]+(?:['’‐‑‒–—-][\\p{L}\\p{N}]+)*/u",
            $this->readableText($markdown),
        );

        return $count === false ? 0 : $count;
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
        for ($ancestor = $node->parent(); $ancestor !== null; $ancestor = $ancestor->parent()) {
            if (
                $ancestor instanceof Code
                || $ancestor instanceof FencedCode
                || $ancestor instanceof IndentedCode
                || $ancestor instanceof Image
                || $ancestor instanceof Prompt
            ) {
                return true;
            }
        }

        return false;
    }
}
