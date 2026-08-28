<?php

namespace App\View\Components\Og;

use App\Services\MetaBag;
use Astrotomic\OpenGraph\OpenGraph;
use Astrotomic\OpenGraph\Twitter;
use Carbon\Carbon;
use Illuminate\View\Component;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;

class Article extends Component
{
    protected MetaBag $meta;

    protected EntryContract $post;

    public function __construct(MetaBag $meta, EntryContract $post)
    {
        $this->meta = $meta;
        $this->post = $post;
    }

    public function render(): string
    {
        $author = Entry::find((string) $this->post->value('author'));
        $authorUrl = $author instanceof EntryContract
            ? route('blog.author.index', ['author' => $author->slug()])
            : url('/');
        $twitter = $author instanceof EntryContract ? (string) $author->value('twitter') : '';

        return implode(PHP_EOL, [
            OpenGraph::article($this->meta->title)
                ->url(url()->current())
                ->when($this->meta->description)->description($this->meta->description)
                ->author($authorUrl)
                ->publishedAt($this->post->date())
                ->modifiedAt(Carbon::createFromTimestampUTC(filemtime($this->post->path())))
                ->when($this->meta->image)->image($this->meta->image)
                ->locale(str_replace('-', '_', app()->getLocale())),
            Twitter::summaryLargeImage($this->meta->title)
                ->when($this->meta->description)->description($this->meta->description)
                ->when($this->meta->image)->image($this->meta->image)
                ->site(config('app.name'))
                ->when($twitter)->creator($twitter),
        ]);
    }
}
