<?php

namespace App\View\Components\Og;

use Astrotomic\OpenGraph\OpenGraph;
use Astrotomic\OpenGraph\Twitter;
use Illuminate\View\Component;
use Statamic\Contracts\Entries\Entry as EntryContract;

class Article extends Component
{
    protected EntryContract $post;

    protected string $siteName;

    public function __construct(EntryContract $post, string $siteName)
    {
        $this->post = $post;
        $this->siteName = $siteName;
    }

    public function render(): string
    {
        $title = $this->post->value('title').' | '.$this->siteName;
        $description = (string) $this->post->value('description');

        return implode(PHP_EOL, [
            OpenGraph::article($title)
                ->url((string) $this->post->absoluteUrl())
                ->when($description)->description($description)
                ->publishedAt($this->post->date())
                ->modifiedAt($this->post->last_modified_at->toDateTime())
                ->locale(str_replace('-', '_', app()->getLocale())),
            Twitter::summaryLargeImage($title)
                ->when($description)->description($description),
        ]);
    }
}
