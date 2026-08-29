<?php

namespace App\Providers;

use App\Repositories\AuthorRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\JobRepository;
use App\Repositories\PostRepository;
use App\Services\FencedCodeRenderer;
use App\Services\ImageRenderer;
use App\Services\MetaBag;
use App\Services\ParagraphRenderer;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Inline\Element\Image;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerMeta();
        $this->registerRepositories();
        $this->registerCommonmark();
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        Event::listen(RequestHandled::class, fn () => $this->registerMeta());
    }

    public function registerMeta(): void
    {
        $this->app->singleton(MetaBag::class);

        View::share('meta', $this->app->make(MetaBag::class));
    }

    public function registerRepositories(): void
    {
        $this->app->singleton(PostRepository::class);
        $this->app->singleton(AuthorRepository::class);
        $this->app->singleton(CategoryRepository::class);
        $this->app->singleton(JobRepository::class);
    }

    public function registerCommonmark(): void
    {
        $this->app->singleton(ConverterInterface::class, \App\Services\CommonMarkConverter::class);
        $this->app->alias(ConverterInterface::class, CommonMarkConverter::class);
        $this->app->alias(ConverterInterface::class, 'markdown');

        $commonMark = $this->app->make(ConverterInterface::class);
        /** @var \League\CommonMark\Environment $environment */
        $environment = $commonMark->getEnvironment();
        $environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer());
        $environment->addBlockRenderer(Paragraph::class, new ParagraphRenderer());
        $environment->addInlineRenderer(Image::class, new ImageRenderer());
    }
}
