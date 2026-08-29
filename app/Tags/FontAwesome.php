<?php

namespace App\Tags;

use App\Services\FontAwesomeIcons;
use DOMDocument;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;
use Statamic\Tags\Tags;

class FontAwesome extends Tags
{
    /** Change this to update the default version used by every icon. */
    public const string DEFAULT_VERSION = '7.2.0';

    private const array SET_STYLES = [
        'brands' => ['brands'],
        'classic' => ['solid', 'regular', 'light', 'thin'],
        'duotone' => ['solid', 'regular', 'light', 'thin'],
        'sharp' => ['solid', 'regular', 'light', 'thin'],
        'sharp-duotone' => ['solid', 'regular', 'light', 'thin'],
    ];

    private const array INTERNAL_PARAMETERS = [
        'version',
        'set',
        'pack',
        'style',
        'icon',
    ];

    public function __construct(private readonly FontAwesomeIcons $icons) {}

    /**
     * Render a cached Font Awesome SVG inline.
     */
    public function index(): HtmlString
    {
        $version = trim((string) $this->params->get('version', self::DEFAULT_VERSION));
        $set = trim((string) $this->params->get(['set', 'pack']));
        $style = trim((string) $this->params->get('style'));
        $icon = trim((string) $this->params->get('icon'));

        $this->validate($version, $set, $style, $icon);

        $svg = $this->icons->get($version, $set, $style, $icon);

        return new HtmlString($this->applyAttributes($svg));
    }

    private function validate(string $version, string $set, string $style, string $icon): void
    {
        if (! preg_match('/\A\d+\.\d+\.\d+\z/', $version)) {
            throw new InvalidArgumentException('The Font Awesome version must be a full MAJOR.MINOR.PATCH version.');
        }

        if (! isset(self::SET_STYLES[$set])) {
            throw new InvalidArgumentException("Unknown Font Awesome set [{$set}].");
        }

        if (! in_array($style, self::SET_STYLES[$set], true)) {
            throw new InvalidArgumentException("The Font Awesome style [{$style}] is not valid for set [{$set}].");
        }

        if (! preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $icon)) {
            throw new InvalidArgumentException('The Font Awesome icon must be a lowercase kebab-case name.');
        }
    }

    private function applyAttributes(string $svg): string
    {
        $document = new DOMDocument;
        $document->loadXML($svg, LIBXML_NONET | LIBXML_NOBLANKS);
        $root = $document->documentElement;
        $attributes = $this->params->except(self::INTERNAL_PARAMETERS);

        if (! $attributes->has('aria-label') && ! $attributes->has('aria-hidden')) {
            $attributes->put('aria-hidden', 'true');
        }

        foreach ($attributes as $name => $value) {
            $name = str_replace('_', '-', (string) $name);

            if (! preg_match('/\A[a-zA-Z_:][a-zA-Z0-9_.:-]*\z/', $name)
                || in_array(strtolower($name), ['xmlns', 'viewbox'], true)
                || str_starts_with(strtolower($name), 'on')) {
                continue;
            }

            if ($value === false || $value === null) {
                $root->removeAttribute($name);

                continue;
            }

            $root->setAttribute($name, $value === true ? $name : (string) $value);
        }

        return (string) $document->saveXML($root);
    }
}
