<?php

namespace App\View\Components\Og;

use App\Services\MetaBag;
use Astrotomic\OpenGraph\OpenGraph;
use Astrotomic\OpenGraph\Twitter;
use Illuminate\View\Component;

class Profile extends Component
{
    protected MetaBag $meta;

    public function __construct(MetaBag $meta)
    {
        $this->meta = $meta;
    }

    public function render(): string
    {
        return implode(PHP_EOL, [
            OpenGraph::profile($this->meta->title)
                ->url(url()->current())
                ->when($this->meta->description)->description($this->meta->description)
                ->when($this->meta->image)->image($this->meta->image)
                ->locale(str_replace('-', '_', app()->getLocale())),
            Twitter::summaryLargeImage($this->meta->title)
                ->when($this->meta->description)->description($this->meta->description)
                ->when($this->meta->image)->image($this->meta->image)
                ->site(config('app.name'))
                ->creator('@devgummibeer'),
        ]);
    }
}
