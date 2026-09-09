<?php

namespace App\Services;

use App\Contracts\StreamTranscriptProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class YouTubeTranscript implements StreamTranscriptProvider
{
    public function fetch(string $videoId): ?string
    {
        $token = (string) config('services.youtube_transcript.token');

        if (blank($token)) {
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$token,
        ])
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->post('https://www.youtube-transcript.io/api/transcripts', [
                'ids' => [$videoId],
            ])
            ->throw()
            ->json();

        if (! is_array($response)) {
            throw new RuntimeException('YouTube Transcript API returned an invalid response.');
        }

        $video = $this->video($response, $videoId);

        if ($video === null) {
            return null;
        }

        return $this->text($video);
    }

    /**
     * @param  array<array-key, mixed>  $response
     * @return array<string, mixed>|null
     */
    private function video(array $response, string $videoId): ?array
    {
        if (($response['id'] ?? null) === $videoId) {
            return $response;
        }

        foreach ($response as $video) {
            if (is_array($video) && ($video['id'] ?? null) === $videoId) {
                return $video;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $video
     */
    private function text(array $video): ?string
    {
        $text = $video['text'] ?? null;

        if (is_string($text) && filled($text)) {
            return trim($text);
        }

        $tracks = $video['tracks'] ?? null;

        if (! is_array($tracks)) {
            return null;
        }

        foreach ($tracks as $track) {
            if (! is_array($track) || ! is_array($track['transcript'] ?? null)) {
                continue;
            }

            $segments = [];

            foreach ($track['transcript'] as $segment) {
                $segmentText = is_array($segment) ? ($segment['text'] ?? null) : null;

                if (is_string($segmentText) && filled($segmentText)) {
                    $segments[] = trim($segmentText);
                }
            }

            if ($segments !== []) {
                return implode(PHP_EOL, $segments);
            }
        }

        return null;
    }
}
