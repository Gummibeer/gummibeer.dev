<?php

namespace App\Tags;

use Astrotomic\Ecologi\Ecologi as EcologiClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Statamic\Tags\Tags;
use Throwable;

class Ecologi extends Tags
{
    public function __construct(private readonly EcologiClient $client) {}

    /**
     * Expose Ecologi's combined reporting totals to an Antlers tag pair.
     */
    public function impact(): array
    {
        $username = trim((string) $this->params->get('username'));
        $fallback = $this->fallbackImpact();

        if (! preg_match('/\A[a-z0-9][a-z0-9-]*\z/i', $username)) {
            return $fallback;
        }

        $key = 'ecologi.impact.'.hash('sha256', strtolower($username));

        try {
            return Cache::flexible($key, [6 * 60 * 60, 7 * 24 * 60 * 60], function () use ($username): array {
                $impact = $this->client->reporting()->getImpact($username);

                return $this->formatImpact($impact->trees, $impact->carbonOffset, true);
            });
        } catch (Throwable $exception) {
            Log::warning('Unable to refresh Ecologi impact totals.', [
                'exception' => $exception::class,
                'username' => $username,
            ]);

            return $fallback;
        }
    }

    private function fallbackImpact(): array
    {
        return $this->formatImpact(
            (int) $this->params->get('fallback_trees', 0),
            (float) $this->params->get('fallback_carbon_offset', 0),
            false,
        );
    }

    private function formatImpact(int $trees, float $carbonOffset, bool $live): array
    {
        return [
            'trees' => $trees,
            'trees_formatted' => number_format($trees),
            'carbon_offset' => $carbonOffset,
            'carbon_offset_formatted' => number_format($carbonOffset, 2),
            'live' => $live,
        ];
    }
}
