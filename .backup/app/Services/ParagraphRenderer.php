<?php

namespace App\Services;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\Image;

class ParagraphRenderer implements BlockRendererInterface
{
    /**
     * @param  Paragraph  $block
     * @return HtmlElement|string
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, bool $inTightList = false)
    {
        if (! ($block instanceof Paragraph)) {
            throw new InvalidArgumentException('Incompatible block type: '.get_class($block));
        }

        $children = $block->children();

        if (count($children) === 1 && Arr::first($children) instanceof Image) {
            return $htmlRenderer->renderInlines($children);
        }

        if ($inTightList) {
            return $htmlRenderer->renderInlines($children);
        }

        $attrs = $block->getData('attributes', []);

        return new HtmlElement('p', $attrs, $htmlRenderer->renderInlines($children));
    }
}
