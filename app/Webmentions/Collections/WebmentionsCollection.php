<?php

namespace App\Webmentions\Collections;

use App\Webmentions\Models\Entry;
use App\Webmentions\Models\Like;
use App\Webmentions\Models\Mention;
use App\Webmentions\Models\Reply;
use App\Webmentions\Models\Repost;
use Illuminate\Support\Collection;

class WebmentionsCollection extends Collection
{
    public function likes(): Collection
    {
        return $this->filter(fn (Entry $entry): bool => $entry instanceof Like)->toBase();
    }

    public function mentions(): Collection
    {
        return $this->filter(fn (Entry $entry): bool => $entry instanceof Mention)->toBase();
    }

    public function replies(): Collection
    {
        return $this->filter(fn (Entry $entry): bool => $entry instanceof Reply)->toBase();
    }

    public function reposts(): Collection
    {
        return $this->filter(fn (Entry $entry): bool => $entry instanceof Repost)->toBase();
    }
}
