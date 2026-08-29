<?php

namespace App\Tags;

use Astrotomic\GithubSponsors\Facades\GithubSponsors as GithubSponsorsClient;
use Astrotomic\Unavatar\Unavatar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Statamic\Tags\Tags;
use Throwable;

class GithubSponsors extends Tags
{
    /**
     * Expose a GitHub account's sponsors to an Antlers tag pair.
     */
    public function index(): array
    {
        $login = trim((string) $this->params->get('login'));
        $fallback = $this->fallbackSponsors();

        if (! preg_match('/\A[a-z0-9](?:[a-z0-9-]{0,37}[a-z0-9])?\z/i', $login)
            || blank(config('services.github.sponsors_token'))) {
            return $fallback;
        }

        $key = 'github-sponsors.'.hash('sha256', strtolower($login));

        try {
            return Cache::flexible($key, [6 * 60 * 60, 7 * 24 * 60 * 60], function () use ($login): array {
                $sponsors = GithubSponsorsClient::login($login)
                    ->sponsors(['login', 'name', 'avatarUrl', 'url'])
                    ->map(fn (array $sponsor): array => $this->formatSponsor($sponsor))
                    ->values()
                    ->all();

                return $this->formatSponsors($sponsors, true);
            });
        } catch (Throwable $exception) {
            Log::warning('Unable to refresh GitHub Sponsors.', [
                'exception' => $exception::class,
                'login' => $login,
            ]);

            return $fallback;
        }
    }

    private function fallbackSponsors(): array
    {
        $sponsors = collect($this->params->get('fallback_sponsors', []))
            ->map(fn (mixed $sponsor): array => $this->formatSponsor((array) $sponsor))
            ->filter(fn (array $sponsor): bool => $sponsor['login'] !== '')
            ->values()
            ->all();

        return $this->formatSponsors(
            $sponsors,
            false,
            max((int) $this->params->get('fallback_count', count($sponsors)), count($sponsors)),
        );
    }

    private function formatSponsors(array $sponsors, bool $live, ?int $count = null): array
    {
        $count ??= count($sponsors);

        return [
            'count' => $count,
            'count_formatted' => number_format($count),
            'sponsors' => $sponsors,
            'live' => $live,
        ];
    }

    private function formatSponsor(array $sponsor): array
    {
        $login = trim((string) ($sponsor['login'] ?? ''));

        return [
            'login' => $login,
            'name' => trim((string) ($sponsor['name'] ?? '')) ?: $login,
            'avatar_url' => $sponsor['avatarUrl']
                ?? $sponsor['avatar_url']
                ?? ($login !== '' ? (new Unavatar($login, 'github'))->toUrl() : null),
            'url' => $sponsor['url'] ?? ($login !== '' ? "https://github.com/{$login}" : null),
            'type' => $sponsor['__typename'] ?? null,
        ];
    }
}
