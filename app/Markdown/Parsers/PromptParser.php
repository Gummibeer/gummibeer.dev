<?php

namespace App\Markdown\Parsers;

use App\Markdown\Nodes\Prompt;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class PromptParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('@\[([^\]]+)\]\((assets\/[^\s)]+\.prompt)\)')->caseSensitive();
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $container = $inlineContext->getContainer();

        if ($container->firstChild() !== null) {
            return false;
        }

        $cursor = $inlineContext->getCursor();
        $endCursor = clone $cursor;
        $endCursor->advanceBy($inlineContext->getFullMatchLength());

        if (! $endCursor->isBlank()) {
            return false;
        }

        [$title, $path] = $inlineContext->getSubMatches();

        $cursor->advanceBy($inlineContext->getFullMatchLength());
        $container->appendChild(new Prompt($title, $path));

        return true;
    }
}
