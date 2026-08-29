<?php /** @var App\Services\Paginator $posts */ ?>
<?php /** @var App\Author $author */ ?>

@extends('web')

@push('head')
    <x-og.website/>
    <x-link-pagination :paginator="$posts"/>
    <x-link-feed route="blog.author.feed" :parameters="compact('author')"/>
@endpush

@section('content')
    <x-section>
        <h1 class="mb-8 text-6xl font-black leading-none text-night-0 dark:text-white">Posts by {{ $author->name }}</h1>
        <div class="grid grid-cols-1 grid-flow-row gap-4 mb-8 md:grid-cols-2 lg:grid-cols-3 md:gap-8 lg:gap-10 xl:gap-12">
            @foreach($posts as $post)
                <x-post.preview :post="$post"></x-post.preview>
            @endforeach
        </div>
        <x-pagination :paginator="$posts"></x-pagination>
    </x-section>
@endsection