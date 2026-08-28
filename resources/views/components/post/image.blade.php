<?php /** @var Statamic\Contracts\Entries\Entry $post */ ?>

@if($post->images)
    <div class="overflow-hidden mb-8 aspect-video" x-data="slider(3)" x-cloak>
        @foreach($post->images as $image)
        <x-img
            :src="$image"
            width="768"
            ratio="16:9"
            :alt="$post->title"
        />
        @endforeach
    </div>
@elseif($post->image)
    <x-figure class="mb-8">
        <x-img
            :src="$post->image"
            width="768"
            ratio="16:9"
            :alt="$post->title"
        />
        <x-slot name="caption">{{ $post->image_credits }}</x-slot>
    </x-figure>
@endif