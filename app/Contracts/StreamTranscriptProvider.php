<?php

namespace App\Contracts;

interface StreamTranscriptProvider
{
    public function fetch(string $videoId): ?string;
}
