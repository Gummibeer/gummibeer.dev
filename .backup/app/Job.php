<?php

namespace App;

use App\Repositories\JobRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Sheets\Sheet;

/**
 * @property-read string $name
 * @property-read Carbon $start_at
 * @property-read Carbon|null $end_at
 * @property-read string $role
 * @property-read string $icon
 * @property-read string $website
 * @property-read string $website_host
 * @property-read string|null $logo
 * @property-read string[] $stack
 * @property-read int $salary
 *
 * @method static Collection|Job[] all()
 */
final class Job extends Sheet
{
    public function getStartAtAttribute(string $value): Carbon
    {
        return Carbon::createFromTimestampUTC($value)->startOfDay();
    }

    public function getEndAtAttribute(?string $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        return Carbon::createFromTimestampUTC($value)->endOfDay();
    }

    public function getWebsiteHostAttribute(): string
    {
        return parse_url($this->website, PHP_URL_HOST);
    }

    public function getIconAttribute(string $value): string
    {
        return 'fal '.Str::start($value, 'fa-');
    }

    public function hasEnd(): bool
    {
        return $this->end_at !== null;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([app(JobRepository::class), $name], $arguments);
    }
}
