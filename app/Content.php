<?php

namespace App;

use App\Repositories\ContentRepository;
use App\Services\Model;
use Illuminate\Support\Collection;

/**
 * @method static Collection|Content[] all(string $collection = 'static')
 * @method static Content find(string $slug, string $collection = 'static')
 */
final class Content extends Model
{
    public static function __callStatic($name, $arguments)
    {
        return app(ContentRepository::class)->{$name}(...$arguments);
    }
}
