<?php

namespace App\Services;

use App\View\Components\Img;
use InvalidArgumentException;
use League\CommonMark\Block\Renderer\FencedCodeRenderer as BaseFencedCodeRenderer;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

class ImageRenderer implements InlineRendererInterface
{
    protected BaseFencedCodeRenderer $baseRenderer;

    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (! ($inline instanceof Image)) {
            throw new InvalidArgumentException('Incompatible inline type: '.get_class($inline));
        }

        $alt = preg_replace(
            '/\<[^>]*\>/',
            '',
            preg_replace(
                '/\<[^>]*alt="([^"]*)"[^>]*\>/',
                '$1',
                $htmlRenderer->renderInlines($inline->children())
            )
        );

        $component = app(Img::class, [
            'src' => $inline->getUrl(),
            'alt' => $alt,
        ]);

        return $component->resolveView()
            ->with($component->data());
    }
}
