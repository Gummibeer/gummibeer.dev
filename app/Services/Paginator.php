<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Statamic\Facades\Blink;

class Paginator extends LengthAwarePaginator
{
    protected ?array $route = null;

    protected ?string $firstPageUrl = null;

    public static function make(Collection $items, int $perPage, ?int $page = null, array $options = []): self
    {
        $page = $page ?? (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        $paginator = new static(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );

        $paginator->withPath(url()->current());
        Blink::put('tag-paginator', $paginator);

        return $paginator;
    }

    public function withRoute(string $route, array $parameters = [], ?string $firstPageUrl = null): self
    {
        $this->route = [
            'name' => $route,
            'parameters' => $parameters,
        ];
        $this->firstPageUrl = $firstPageUrl;

        return $this;
    }

    public function url($page): string
    {
        $page = min(max(1, $page), $this->lastPage());

        if ($this->route === null) {
            return parent::url($page);
        }

        if ($page === 1 && $this->firstPageUrl !== null) {
            return url($this->firstPageUrl);
        }

        if ($page === 1) {
            return url()->route($this->route['name'], $this->route['parameters'], true);
        }

        $route = app('router')->getRoutes()->getByName($this->route['name']);

        return url()->toRoute($route, array_merge($this->route['parameters'], ['page' => 'p:'.$page]), true);
    }
}
