<?php

namespace App\Services;

use DOMDocument;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FontAwesomeIcons
{
    private const string BASE_URL = 'https://icons.test';

    private const int MAX_SVG_BYTES = 1_000_000;

    public function __construct(private readonly HttpFactory $http) {}

    /**
     * Retrieve an icon from private storage or download and persist it forever.
     */
    public function get(string $version, string $set, string $style, string $icon): string
    {
        $path = "font-awesome/v{$version}/{$set}/{$style}/{$icon}.svg";
        $disk = Storage::disk('local');

        if ($disk->exists($path)) {
            $svg = $disk->get($path);

            if ($this->isSvg($svg)) {
                return $svg;
            }

            $disk->delete($path);
        }

        $url = implode('/', [
            self::BASE_URL,
            'v'.rawurlencode($version),
            rawurlencode($set),
            rawurlencode($style),
            rawurlencode($icon).'.svg',
        ]);

        $response = $this->http
            ->accept('image/svg+xml')
            ->timeout(15)
            ->retry(2, 200)
            ->get($url);

        $svg = $this->validatedBody($response, $url);

        if (! $disk->put($path, $svg)) {
            throw new RuntimeException("Unable to cache Font Awesome icon [{$path}].");
        }

        return $svg;
    }

    private function validatedBody(Response $response, string $url): string
    {
        if (! $response->successful()) {
            throw new RuntimeException("Font Awesome icon request failed with HTTP {$response->status()} [{$url}].");
        }

        $contentType = strtolower(trim(strtok((string) $response->header('Content-Type'), ';')));

        if ($contentType !== 'image/svg+xml') {
            throw new RuntimeException("Font Awesome icon response has an invalid content type [{$contentType}].");
        }

        $svg = $response->body();

        if (! $this->isSvg($svg)) {
            throw new RuntimeException('Font Awesome icon response is not a valid SVG document.');
        }

        return $svg;
    }

    private function isSvg(string $svg): bool
    {
        if ($svg === '' || strlen($svg) > self::MAX_SVG_BYTES) {
            return false;
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument;
        $loaded = $document->loadXML($svg, LIBXML_NONET | LIBXML_NOBLANKS);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $loaded
            && $document->documentElement !== null
            && strtolower($document->documentElement->localName) === 'svg';
    }
}
