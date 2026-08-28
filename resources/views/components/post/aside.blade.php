<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Statamic\Contracts\Entries\Entry $post */ ?>

<aside {{ $attributes->except('post')->merge(['class' => 'text-snow-20 dark:text-snow-10']) }}>
    <ul class="flex flex-col list-none sm:flex-row sm:space-x-3">
        <li>
            <x-icon class="mr-1 fal fa-calendar"/>
            <a href="{{ route('blog.year.index', $post->date->year) }}" class="hover:text-brand">
                <time datetime="{{ $post->date->format('Y-m-d') }}">
                    {{ $post->date->format('M jS, Y') }}
                </time>
            </a>
        </li>
        <li>
            <x-icon class="mr-1 fal fa-clock"/>
            {{ $post->read_time }} min read
        </li>
{{--        <li>--}}
{{--            <x-icon class="mr-1 fal fa-user"/>--}}
{{--            <a href="{{ route('blog.author.index', ['author' => $post->author->slug()]) }}" class="hover:text-brand">{{ $post->author->title }}</a>--}}
{{--        </li>--}}
    </ul>
</aside>