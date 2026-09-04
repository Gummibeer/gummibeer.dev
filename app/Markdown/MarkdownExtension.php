<?php

namespace App\Markdown;

use App\Markdown\Nodes\Prompt;
use App\Markdown\Parsers\PromptParser;
use App\Markdown\Renderers\FencedCodeRenderer;
use App\Markdown\Renderers\ImageRenderer;
use App\Markdown\Renderers\ParagraphRenderer;
use App\Markdown\Renderers\PromptRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Block\Paragraph;

final class MarkdownExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addInlineParser(new PromptParser, 100)
            ->addRenderer(Prompt::class, new PromptRenderer, 10)
            ->addRenderer(FencedCode::class, new FencedCodeRenderer, 10)
            ->addRenderer(Paragraph::class, new ParagraphRenderer, 10)
            ->addRenderer(Image::class, new ImageRenderer, 10);
    }
}
