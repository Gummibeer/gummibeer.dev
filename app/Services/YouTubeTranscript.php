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
            throw new RuntimeException('YOUTUBE_TRANSCRIPT_API_TOKEN is not configured.');
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
     * @return array<array-key, mixed>|null
     */
    private function video(array $response, string $videoId): ?array
    {
        $videos = $response['data'] ?? $response;

        if (! is_array($videos)) {
            return null;
        }

        if (($videos['id'] ?? null) === $videoId) {
            return $videos;
        }

        foreach ($videos as $video) {
            if (is_array($video) && ($video['id'] ?? null) === $videoId) {
                return $video;
            }
        }

        if (count($videos) === 1) {
            $video = reset($videos);

            return is_array($video) ? $video : null;
        }

        return null;
    }

    /**
     * @param  array<array-key, mixed>  $video
     */
    private function text(array $video): ?string
    {
        $tracks = $video['tracks'] ?? null;

        if (is_array($tracks)) {
            foreach ($tracks as $track) {
                if (! is_array($track) || ! is_array($track['transcript'] ?? null)) {
                    continue;
                }

                $text = $this->segments($track['transcript']);

                if (filled($text)) {
                    return $text;
                }
            }
        }

        $transcript = $video['transcript'] ?? null;

        if (is_array($transcript)) {
            $text = $this->segments($transcript);

            if (filled($text)) {
                return $text;
            }
        }

        if (is_string($transcript) && filled($transcript)) {
            return trim($transcript);
        }

        $text = $video['text'] ?? null;

        return is_string($text) && filled($text) ? trim($text) : null;
    }

    /**
     * @param  array<array-key, mixed>  $segments
     */
    private function segments(array $segments): ?string
    {
        $lines = [];

        foreach ($segments as $segment) {
            if (! is_array($segment)) {
                continue;
            }

            $text = $segment['text'] ?? null;

            if (! is_string($text)) {
                continue;
            }

            $text = trim($text);

            if ($text === '') {
                continue;
            }

            $start = $segment['start'] ?? null;

            $lines[] = is_numeric($start)
                ? sprintf('[%s] %s', $this->timestamp((float) $start), $text)
                : $text;
        }

        return $lines === [] ? null : implode(PHP_EOL, $lines);
    }

    private function timestamp(float $seconds): string
    {
        $seconds = max(0, (int) floor($seconds));
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $seconds %= 60;

        return $hours > 0
            ? sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds)
            : sprintf('%02d:%02d', $minutes, $seconds);
    }
}
