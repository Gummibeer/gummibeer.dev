<?php

namespace App;

use App\Repositories\JobRepository;
use App\Services\Model;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
final class Job extends Model
{
    public function getStartAtAttribute(string|int|CarbonInterface $value): Carbon
    {
        return $this->date($value)->startOfDay();
    }

    public function getEndAtAttribute(string|int|CarbonInterface|null $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        return $this->date($value)->endOfDay();
    }

    public function getWebsiteHostAttribute(mixed $value = null): string
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
        return app(JobRepository::class)->{$name}(...$arguments);
    }

    private function date(string|int|CarbonInterface $value): Carbon
    {
        if ($value instanceof CarbonInterface) {
            return Carbon::instance($value);
        }

        return is_numeric($value)
            ? Carbon::createFromTimestampUTC((int) $value)
            : Carbon::parse($value);
    }
}
