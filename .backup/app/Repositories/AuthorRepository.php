<?php

namespace App\Repositories;

use App\Author;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AuthorRepository
{
    public function all(): Collection
    {
        return collect([
            [
                'nickname' => 'Gummibeer',
                'email' => 'dev@gummibeer.de',
                'firstname' => 'Tom',
                'lastname' => 'Witkowski',
                'payment_pointer' => '$ilp.uphold.com/EagWEdJU64mN',
                'twitter' => '@devgummibeer',
            ],
        ])
            ->mapInto(Author::class);
    }

    public function find(string $nickname): Author
    {
        return $this->all()
            ->first(fn (Author $author): bool => Str::slug($author->nickname) === Str::slug($nickname));
    }
}
