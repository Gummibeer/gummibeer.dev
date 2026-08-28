<?php

namespace App\Services;

use LogicException;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Globals\Variables;

final class SiteIdentity
{
    private const GLOBAL_HANDLE = 'site';

    public function siteName(): string
    {
        return $this->string('site_name');
    }

    public function brandName(): string
    {
        return $this->string('brand_name');
    }

    public function tagline(): string
    {
        return $this->string('tagline');
    }

    public function copyrightName(): string
    {
        return $this->string('copyright_name');
    }

    public function copyrightSince(): int
    {
        $value = $this->value('copyright_since');

        if (! is_int($value) && ! (is_string($value) && ctype_digit($value))) {
            throw new LogicException('The site copyright_since global must be an integer.');
        }

        return (int) $value;
    }

    public function phone(): string
    {
        return $this->string('phone');
    }

    public function phoneUrl(): string
    {
        return 'tel:'.(string) preg_replace('/[^\d+]/', '', $this->phone());
    }

    public function email(): string
    {
        return $this->string('email');
    }

    public function emailUrl(): string
    {
        return 'mailto:'.$this->email();
    }

    public function telegramUsername(): string
    {
        return $this->string('telegram_username');
    }

    public function telegramLabel(): string
    {
        return '@'.ltrim($this->telegramUsername(), '@');
    }

    public function telegramUrl(): string
    {
        return 'https://t.me/'.ltrim($this->telegramUsername(), '@');
    }

    public function twitterUrl(): string
    {
        return $this->string('twitter_url');
    }

    public function twitterHandle(): string
    {
        return $this->string('twitter_handle');
    }

    public function githubUrl(): string
    {
        return $this->string('github_url');
    }

    public function instagramUrl(): string
    {
        return $this->string('instagram_url');
    }

    public function stravaUrl(): string
    {
        return $this->string('strava_url');
    }

    public function steamUrl(): string
    {
        return $this->string('steam_url');
    }

    public function imprintLabel(): string
    {
        return $this->string('imprint_label');
    }

    public function imprintUrl(): string
    {
        return $this->pageUrl('imprint_page');
    }

    public function privacyLabel(): string
    {
        return $this->string('privacy_label');
    }

    public function privacyUrl(): string
    {
        return $this->pageUrl('privacy_page');
    }

    private function pageUrl(string $key): string
    {
        $entry = Entry::find($this->string($key));

        if (! $entry instanceof StatamicEntry) {
            throw new LogicException("The site {$key} global must reference a Statamic entry.");
        }

        return (string) $entry->absoluteUrl();
    }

    private function string(string $key): string
    {
        $value = $this->value($key);

        if (! is_string($value) || $value === '') {
            throw new LogicException("The site {$key} global must be a non-empty string.");
        }

        return $value;
    }

    private function value(string $key): mixed
    {
        return $this->variables()->get($key);
    }

    private function variables(): Variables
    {
        $global = GlobalSet::findByHandle(self::GLOBAL_HANDLE);

        if ($global === null) {
            throw new LogicException('The site Statamic global set is missing.');
        }

        $variables = $global->inCurrentSite();

        if (! $variables instanceof Variables) {
            throw new LogicException('The current site has no site global variables.');
        }

        return $variables;
    }
}
