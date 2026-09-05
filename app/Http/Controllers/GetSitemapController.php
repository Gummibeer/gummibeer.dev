<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;
use Symfony\Component\HttpFoundation\Response;

class GetSitemapController
{
    public function __invoke(Request $request): Response
    {
        $sitemap = Sitemap::create();

        Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->get()
            ->each(function (EntryContract $entry) use ($sitemap): void {
                $sitemap->add(
                    Url::create($entry->absoluteUrl())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                        ->setLastModificationDate($entry->last_modified_at)
                        ->setPriority(1)
                );
            });

        Entry::query()
            ->where('collection', 'pages')
            ->whereStatus('published')
            ->get()
            ->each(function (EntryContract $entry) use ($sitemap): void {
                $sitemap->add(
                    Url::create($entry->absoluteUrl())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.5)
                );
            });

        return $sitemap->toResponse($request);
    }
}
