<?php /** @var App\Services\Paginator $posts */ ?>
<?php /** @var int $year */ ?>

@extends ('web')

@section ('content')
    <x-section>
        <h1 class="mb-8 text-6xl leading-none font-black text-night-0 dark:text-white">Posts from {{ $year }}</h1>
        <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
            @foreach ($posts as $post)
                <x-post.preview :post="$post"></x-post.preview>
            @endforeach
        </div>
        <x-pagination :paginator="$posts"></x-pagination>
    </x-section>
@endsection
