<?php /** @var App\Services\Paginator $posts */ ?>
<?php /** @var Statamic\Taxonomies\LocalizedTerm $category */ ?>

@extends ('web')

@push ('head')
    <x-og.website />
    <x-link-pagination :paginator="$posts" />
    <x-link-feed
        route="blog.category.feed"
        :parameters="['category' => $category->slug()]"
    />
@endpush

@section ('content')
    <x-section>
        <h1 class="mb-8 text-6xl leading-none font-black text-night-0 dark:text-white">Posts about {{ $category->title }}</h1>
        <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
            @foreach ($posts as $post)
                <x-post.preview :post="$post"></x-post.preview>
            @endforeach
        </div>
        <x-pagination :paginator="$posts"></x-pagination>
    </x-section>
@endsection
