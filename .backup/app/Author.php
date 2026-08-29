<?php

namespace App;

use App\Repositories\AuthorRepository;
use App\Services\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property-read string $nickname
 * @property-read string $email
 * @property-read string $firstname
 * @property-read string $lastname
 * @property-read string $name
 * @property-read string $url
 * @property-read string $avatar
 * @property-read string $payment_pointer
 * @property-read string $twitter
 *
 * @method static Collection|Author[] all()
 * @method static Author find(string $nickname)
 */
final class Author extends Model
{
    /**
     * @return Collection|Post[]
     */
    public function posts(): Collection
    {
        return Post::all()->filter(fn (Post $post): bool => $post->author->nickname === $this->nickname);
    }

    public function getRouteKey()
    {
        return Str::slug($this->nickname);
    }

    public function getNameAttribute(): string
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function getUrlAttribute(): string
    {
        return route('blog.author.index', $this);
    }

    public function getAvatarAttribute(): string
    {
        return sprintf(
            'https://secure.gravatar.com/avatar/%s?s=512&r=g&d=mp',
            hash('md5', strtolower(trim($this->email)))
        );
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([app(AuthorRepository::class), $name], $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([app(AuthorRepository::class), $name], $arguments);
    }
}
