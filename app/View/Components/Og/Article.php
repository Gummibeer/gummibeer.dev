<?php

namespace App\View\Components\Og;

use Astrotomic\OpenGraph\OpenGraph;
use Astrotomic\OpenGraph\Twitter;
use Carbon\Carbon;
use Illuminate\View\Component;
use Statamic\Contracts\Auth\User as UserContract;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\User;

class Article extends Component
{
    protected EntryContract $post;

    protected string $siteName;

    protected string $twitterHandle;

    public function __construct(EntryContract $post, string $siteName, string $twitterHandle)
    {
        $this->post = $post;
        $this->siteName = $siteName;
        $this->twitterHandle = $twitterHandle;
    }

    public function render(): string
    {
        $title = $this->post->value('title').' | '.$this->siteName;
        $description = (string) $this->post->value('description');
        $author = User::find((string) $this->post->value('author'));
        $twitter = $author instanceof UserContract ? (string) $author->get('twitter') : '';

        return implode(PHP_EOL, [
            OpenGraph::article($title)
                ->url((string) $this->post->absoluteUrl())
                ->when($description)->description($description)
                ->publishedAt($this->post->date())
                ->modifiedAt(Carbon::createFromTimestampUTC(filemtime($this->post->path())))
                ->locale(str_replace('-', '_', app()->getLocale())),
            Twitter::summaryLargeImage($title)
                ->when($description)->description($description)
                ->site($this->twitterHandle)
                ->when($twitter)->creator($twitter),
        ]);
    }
}
