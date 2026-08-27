<?php

namespace App\Services;

use InvalidArgumentException;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class ParagraphRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable|string|null
    {
        if (! $node instanceof Paragraph) {
            throw new InvalidArgumentException('Incompatible node type: '.get_class($node));
        }

        $firstChild = $node->firstChild();

        if ($firstChild instanceof Image && $firstChild->next() === null) {
            return $childRenderer->renderNodes($node->children());
        }

        return new HtmlElement('p', [], $childRenderer->renderNodes($node->children()));
    }
}
