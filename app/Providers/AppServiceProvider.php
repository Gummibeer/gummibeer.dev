<?php

namespace App\Providers;

use App\Repositories\AuthorRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ContentRepository;
use App\Repositories\JobRepository;
use App\Repositories\PostRepository;
use App\Repositories\StreamRepository;
use App\Services\FencedCodeRenderer;
use App\Services\ImageRenderer;
use App\Services\MetaBag;
use App\Services\ParagraphRenderer;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Image;

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
        $this->app->singleton(StreamRepository::class);
        $this->app->singleton(ContentRepository::class);
    }

    public function registerCommonmark(): void
    {
        $this->app->singleton(ConverterInterface::class, function (): ConverterInterface {
            $environment = new Environment([
                'html_input' => 'allow',
                'allow_unsafe_links' => true,
            ]);

            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addRenderer(FencedCode::class, new FencedCodeRenderer(), 10);
            $environment->addRenderer(Paragraph::class, new ParagraphRenderer(), 10);
            $environment->addRenderer(Image::class, new ImageRenderer(), 10);

            return new MarkdownConverter($environment);
        });

        $this->app->alias(ConverterInterface::class, 'markdown');
    }
}
