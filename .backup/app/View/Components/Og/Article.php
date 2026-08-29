<?php

namespace App\View\Components\Og;

use App\Post;
use App\Services\MetaBag;
use Astrotomic\OpenGraph\OpenGraph;
use Astrotomic\OpenGraph\Twitter;
use Illuminate\View\Component;

class Article extends Component
{
    protected MetaBag $meta;

    protected Post $post;

    public function __construct(MetaBag $meta, Post $post)
    {
        $this->meta = $meta;
        $this->post = $post;
    }

    public function render(): string
    {
        return implode(PHP_EOL, [
            OpenGraph::article($this->meta->title)
                ->url(url()->current())
                ->when($this->meta->description)->description($this->meta->description)
                ->author($this->post->author->url)
                ->publishedAt($this->post->date)
                ->modifiedAt($this->post->modified_at)
                ->when($this->meta->image)->image($this->meta->image)
                ->locale(str_replace('-', '_', app()->getLocale())),
            Twitter::summaryLargeImage($this->meta->title)
                ->when($this->meta->description)->description($this->meta->description)
                ->when($this->meta->image)->image($this->meta->image)
                ->site(config('app.name'))
                ->creator($this->post->author->twitter),
        ]);
    }
}
