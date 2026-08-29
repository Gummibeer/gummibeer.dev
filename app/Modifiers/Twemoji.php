<?php

namespace App\Modifiers;

use Astrotomic\Twemoji\Twemoji as TwemojiRenderer;
use Statamic\Modifiers\Modifier;
use Stringable;

class Twemoji extends Modifier
{
    /**
     * Replace emoji in visible HTML text while preserving tags, attributes, and code.
     */
    public function index(mixed $value): mixed
    {
        if (! is_string($value) && ! $value instanceof Stringable) {
            return $value;
        }

        $parts = preg_split(
            '/(<!--.*?-->|<[^>]+>)/su',
            (string) $value,
            -1,
            PREG_SPLIT_DELIM_CAPTURE,
        );

        if ($parts === false) {
            return $value;
        }

        $protectedElements = [];

        foreach ($parts as &$part) {
            if (str_starts_with($part, '<')) {
                $this->trackProtectedElement($part, $protectedElements);

                continue;
            }

            if ($protectedElements !== [] || $part === '') {
                continue;
            }

            $part = TwemojiRenderer::text($part)->toHtml(null, [
                'width' => '20',
                'height' => '20',
                'loading' => 'lazy',
                'decoding' => 'async',
                'class' => 'twemoji',
            ]);
        }

        unset($part);

        return implode('', $parts);
    }

    private function trackProtectedElement(string $html, array &$protectedElements): void
    {
        if (preg_match('/^<\/(code|pre|script|style|textarea)\s*>/i', $html, $match)) {
            $index = array_search(strtolower($match[1]), $protectedElements, true);

            if ($index !== false) {
                unset($protectedElements[$index]);
                $protectedElements = array_values($protectedElements);
            }

            return;
        }

        if (preg_match('/^<(code|pre|script|style|textarea)\b/i', $html, $match)) {
            $protectedElements[] = strtolower($match[1]);
        }
    }
}
