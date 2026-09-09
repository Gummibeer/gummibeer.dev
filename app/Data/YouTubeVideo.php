<?php

namespace App\Data;

use Carbon\CarbonImmutable;

final readonly class YouTubeVideo
{
    /**
     * @param  list<string>  $tags
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public CarbonImmutable $publishedAt,
        public string $duration,
        public string $channelId,
        public string $channelName,
        public string $channelUrl,
        public string $url,
        public string $image,
        public array $tags,
    ) {}
}
