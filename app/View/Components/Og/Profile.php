<?php

namespace App\View\Components\Og;

use App\Services\MetaBag;
use App\Services\SiteIdentity;
use Astrotomic\OpenGraph\OpenGraph;
use Astrotomic\OpenGraph\Twitter;
use Illuminate\View\Component;

class Profile extends Component
{
    protected MetaBag $meta;

    protected SiteIdentity $identity;

    public function __construct(MetaBag $meta, SiteIdentity $identity)
    {
        $this->meta = $meta;
        $this->identity = $identity;
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
                ->site($this->identity->siteName())
                ->creator($this->identity->twitterHandle()),
        ]);
    }
}
