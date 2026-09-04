<?php

namespace App\Markdown\Nodes;

use League\CommonMark\Node\Inline\AbstractInline;

final class Prompt extends AbstractInline
{
    public function __construct(
        private readonly string $title,
        private readonly string $path,
    ) {
        parent::__construct();
    }

    public function title(): string
    {
        return $this->title;
    }

    public function path(): string
    {
        return $this->path;
    }
}
