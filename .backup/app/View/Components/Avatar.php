<?php

namespace App\View\Components;

use Astrotomic\Unavatar\Laravel\Unavatar;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Avatar extends Component
{
    public string $search;

    public ?string $src = null;

    public ?string $provider = null;

    public function __construct(string $search, ?string $src = null, ?string $provider = null)
    {
        $this->search = $search;
        $this->src = $src;
        $this->provider = $provider;
    }

    public function render(): View
    {
        return view('components.avatar');
    }

    public function url(): string
    {
        if ($this->src) {
            return $this->src;
        }

        return Unavatar::make($this->search, $this->provider)->toUrl();
    }
}
