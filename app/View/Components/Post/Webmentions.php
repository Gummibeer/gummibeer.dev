<?php

namespace App\View\Components\Post;

use App\Webmentions\Client;
use App\Webmentions\Models\Entry;
use App\Webmentions\Models\Repost;
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

        $client = app(Client::class);

        $this->likes = $client->likes($url)
            ->sortByDesc('created_at');

        $this->reposts = $client->reposts($url)
            ->concat($client->mentions($url))
            ->filter(fn (Entry $entry): bool => $entry instanceof Repost || empty($entry->text))
            ->sortByDesc('created_at');

        $this->comments = $client->mentions($url)
            ->concat($client->replies($url))
            ->reject(fn (Entry $entry): bool => empty($entry->text))
            ->sortBy('created_at');
    }

    public function render()
    {
        return view('components.post.webmentions');
    }
}
