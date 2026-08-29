<?php

namespace App\Services;

use Illuminate\Support\HtmlString;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Block\Renderer\FencedCodeRenderer as BaseFencedCodeRenderer;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Util\Xml;

class FencedCodeRenderer implements BlockRendererInterface
{
    protected BaseFencedCodeRenderer $baseRenderer;

    public function __construct()
    {
        $this->baseRenderer = new BaseFencedCodeRenderer();
    }

    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        $element = $this->baseRenderer->render($block, $htmlRenderer, $inTightList);

        return view('components.code', [
            'name' => $this->getFileName($block),
            'lang' => $this->getSpecifiedLanguage($block),
            'slot' => new HtmlString($element->getContents(false)->getContents()),
        ]);
    }

    protected function getSpecifiedLanguage(FencedCode $block): ?string
    {
        $infoWords = $block->getInfoWords();

        if (empty($infoWords) || empty($infoWords[0])) {
            return null;
        }

        return Xml::escape($infoWords[0], true);
    }

    protected function getFileName(FencedCode $block): ?string
    {
        $infoWords = $block->getInfoWords();

        if (empty($infoWords) || empty($infoWords[1])) {
            return null;
        }

        return Xml::escape($infoWords[1], true);
    }
}
