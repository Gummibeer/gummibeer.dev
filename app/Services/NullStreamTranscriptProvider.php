<?php

namespace App\Services;

use App\Contracts\StreamTranscriptProvider;

final class NullStreamTranscriptProvider implements StreamTranscriptProvider
{
    public function fetch(string $videoId): ?string
    {
        return null;
    }
}
