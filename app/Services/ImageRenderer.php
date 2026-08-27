<?php

namespace App\Services;

use App\View\Components\Img;
use InvalidArgumentException;
use League\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class ImageRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable|string|null
    {
        if (! $node instanceof Image) {
            throw new InvalidArgumentException('Incompatible node type: '.get_class($node));
        }

        $alt = strip_tags((string) $childRenderer->renderNodes($node->children()));

        $component = app(Img::class, [
            'src' => $node->getUrl(),
            'alt' => $alt,
        ]);

        return $component->resolveView()
            ->with($component->data())
            ->render();
    }
}
