<?php

namespace App\Markdown\Renderers;

use Illuminate\Support\HtmlString;
use InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\Xml;

class FencedCodeRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable|string|null
    {
        if (! $node instanceof FencedCode) {
            throw new InvalidArgumentException('Incompatible node type: '.get_class($node));
        }

        $infoWords = $node->getInfoWords();

        return view('components.code', [
            'name' => isset($infoWords[1]) ? Xml::escape($infoWords[1]) : null,
            'lang' => isset($infoWords[0]) ? Xml::escape($infoWords[0]) : null,
            'slot' => new HtmlString(htmlspecialchars($node->getLiteral(), ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8')),
        ])->render();
    }
}
