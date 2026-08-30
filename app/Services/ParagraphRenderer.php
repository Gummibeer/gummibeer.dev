<?php

namespace App\Services;

use InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Block\TightBlockInterface;
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

        if ($this->inTightList($node)) {
            return $childRenderer->renderNodes($node->children());
        }

        return new HtmlElement(
            'p',
            $node->data->get('attributes'),
            $childRenderer->renderNodes($node->children()),
        );
    }

    private function inTightList(Paragraph $node): bool
    {
        $levels = 2;

        while (($parent = $node->parent()) && $levels--) {
            if ($parent instanceof TightBlockInterface) {
                return $parent->isTight();
            }

            $node = $parent;
        }

        return false;
    }
}
