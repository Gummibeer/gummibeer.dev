<?php

namespace App\View\Components\Post;

use Astrotomic\Webmentions\Facades\Webmentions as WebmentionsClient;
use Astrotomic\Webmentions\Models\Entry;
use Astrotomic\Webmentions\Models\Repost;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Webmentions extends Component
{
    public Collection $likes;

    public Collection $reposts;

    public Collection $comments;

    public function __construct(?string $url = null)
    {
        $url ??= request()->url();

        $this->likes = WebmentionsClient::likes($url)
            ->sortByDesc('created_at');

        $this->reposts = WebmentionsClient::reposts($url)
            ->concat(WebmentionsClient::mentions($url))
            ->filter(fn (Entry $entry): bool => $entry instanceof Repost || empty($entry->text))
            ->sortByDesc('created_at');

        $this->comments = WebmentionsClient::mentions($url)
            ->concat(WebmentionsClient::replies($url))
            ->reject(fn (Entry $entry): bool => empty($entry->text))
            ->sortBy('created_at');
    }

    public function render()
    {
        return view('components.post.webmentions');
    }
}
