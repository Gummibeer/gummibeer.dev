<?php

namespace App\Services;

use App\Data\YouTubeVideo;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

final class YouTube
{
    public function video(string $url): YouTubeVideo
    {
        $videoId = $this->videoId($url);
        $apiKey = (string) config('services.youtube.key');

        if (blank($apiKey)) {
            throw new RuntimeException('Missing YOUTUBE_API_KEY.');
        }

        $video = Http::baseUrl('https://www.googleapis.com/youtube/v3')
            ->get('videos', [
                'part' => 'snippet,contentDetails',
                'id' => $videoId,
                'key' => $apiKey,
            ])
            ->throw()
            ->json('items.0');

        if (! is_array($video)) {
            throw new RuntimeException("YouTube video [{$videoId}] was not found.");
        }

        $channelId = (string) data_get($video, 'snippet.channelId');

        return new YouTubeVideo(
            id: $videoId,
            title: (string) data_get($video, 'snippet.title'),
            description: (string) data_get($video, 'snippet.description'),
            publishedAt: CarbonImmutable::parse((string) data_get($video, 'snippet.publishedAt')),
            duration: (string) data_get($video, 'contentDetails.duration'),
            channelId: $channelId,
            channelName: (string) data_get($video, 'snippet.channelTitle'),
            channelUrl: 'https://www.youtube.com/channel/'.$channelId,
            url: 'https://youtu.be/'.$videoId,
            image: $this->thumbnail($video),
            tags: array_values(array_map('strval', (array) data_get($video, 'snippet.tags', []))),
        );
    }

    private function videoId(string $value): string
    {
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $value) === 1) {
            return $value;
        }

        $host = strtolower((string) parse_url($value, PHP_URL_HOST));
        $path = trim((string) parse_url($value, PHP_URL_PATH), '/');

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            return $this->validateVideoId(Str::before($path, '/'));
        }

        if (! in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'music.youtube.com'], true)) {
            throw new RuntimeException("Unsupported YouTube URL [{$value}].");
        }

        parse_str((string) parse_url($value, PHP_URL_QUERY), $query);

        if (isset($query['v'])) {
            return $this->validateVideoId((string) $query['v']);
        }

        $segments = explode('/', $path);

        if (count($segments) >= 2 && in_array($segments[0], ['embed', 'live', 'shorts'], true)) {
            return $this->validateVideoId($segments[1]);
        }

        throw new RuntimeException("Could not extract a video ID from [{$value}].");
    }

    private function validateVideoId(string $videoId): string
    {
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) !== 1) {
            throw new RuntimeException("Invalid YouTube video ID [{$videoId}].");
        }

        return $videoId;
    }

    /**
     * @param  array<string, mixed>  $video
     */
    private function thumbnail(array $video): string
    {
        foreach (['maxres', 'standard', 'high', 'medium', 'default'] as $size) {
            $url = data_get($video, "snippet.thumbnails.{$size}.url");

            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        throw new RuntimeException('YouTube video has no thumbnail.');
    }
}
