<?php

namespace App\Services;

use Carbon\Carbon;
use RuntimeException;
use Spatie\SchemaOrg\BlogPosting;
use Spatie\SchemaOrg\BreadcrumbList;
use Spatie\SchemaOrg\Person;
use Spatie\SchemaOrg\ProfilePage;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\WebSite;
use Statamic\Contracts\Auth\User as UserContract;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\GlobalSet;
use Statamic\Globals\Variables;

final class SchemaOrg
{
    private ?Variables $identity = null;

    public function person(): Person
    {
        $identity = $this->identity();

        return Schema::person()
            ->identifier(url('/').'#person')
            ->name((string) $identity->get('brand_name'))
            ->alternateName((string) $identity->get('site_name'))
            ->description((string) $identity->get('tagline'))
            ->url(url('/'))
            ->sameAs($this->socialProfiles());
    }

    public function webSite(): WebSite
    {
        $identity = $this->identity();

        return Schema::webSite()
            ->identifier(url('/').'#website')
            ->name((string) $identity->get('site_name'))
            ->description((string) $identity->get('tagline'))
            ->url(url('/'))
            ->creator($this->person());
    }

    public function profilePage(EntryContract $page): ProfilePage
    {
        $identity = $this->identity();
        $url = (string) $page->absoluteUrl();
        $description = (string) $page->value('description');

        return Schema::profilePage()
            ->identifier($url.'#profile-page')
            ->name((string) $identity->get('site_name'))
            ->description($description ?: (string) $identity->get('tagline'))
            ->url($url)
            ->mainEntity($this->person());
    }

    public function blogPosting(EntryContract $post): BlogPosting
    {
        if (! $post instanceof StatamicEntry) {
            throw new RuntimeException('Expected a concrete Statamic post entry.');
        }

        $url = (string) $post->absoluteUrl();
        $description = (string) $post->value('description');
        $schema = Schema::blogPosting()
            ->identifier($url.'#blog-post')
            ->headline((string) $post->value('title'))
            ->url($url)
            ->mainEntityOfPage($url)
            ->datePublished($post->date())
            ->dateModified(Carbon::createFromTimestampUTC(filemtime($post->path())))
            ->inLanguage(app()->getLocale())
            ->author($this->postAuthor($post));

        if ($description !== '') {
            $schema->description($description);
        }

        return $schema;
    }

    public function breadcrumbs(EntryContract $post): BreadcrumbList
    {
        $url = (string) $post->absoluteUrl();

        return Schema::breadcrumbList()
            ->identifier($url.'#breadcrumbs')
            ->itemListElement([
                Schema::listItem()
                    ->position(1)
                    ->name('Home')
                    ->item(url('/')),
                Schema::listItem()
                    ->position(2)
                    ->name('Blog')
                    ->item(url('/blog')),
                Schema::listItem()
                    ->position(3)
                    ->name((string) $post->value('title'))
                    ->item($url),
            ]);
    }

    private function postAuthor(StatamicEntry $post): Person
    {
        $author = $post->author;

        if (! $author instanceof UserContract) {
            throw new RuntimeException('The Statamic post author is missing.');
        }

        if ($author->get('name') === $this->identity()->get('brand_name')) {
            return $this->person();
        }

        return Schema::person()->name((string) $author->get('name'));
    }

    /**
     * @return list<string>
     */
    private function socialProfiles(): array
    {
        $identity = $this->identity();

        return collect([
            $identity->get('github_url'),
            $identity->get('x_url'),
            $identity->get('instagram_url'),
            $identity->get('steam_url'),
        ])
            ->filter()
            ->map(static fn (mixed $url): string => (string) $url)
            ->values()
            ->all();
    }

    private function identity(): Variables
    {
        if ($this->identity) {
            return $this->identity;
        }

        $identity = GlobalSet::findByHandle('identity');

        if (! $identity) {
            throw new RuntimeException('The Statamic identity global is missing.');
        }

        return $this->identity = $identity->inCurrentSite();
    }
}
