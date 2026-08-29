<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var \Illuminate\Support\Collection|\Astrotomic\Webmentions\Models\Like[] $likes */ ?>
<?php /** @var \Illuminate\Support\Collection|\Astrotomic\Webmentions\Models\Repost[] $reposts */ ?>
<?php /** @var \Illuminate\Support\Collection|\Astrotomic\Webmentions\Models\Mention[]|\Astrotomic\Webmentions\Models\Reply[] $comments */ ?>

@if($likes->isNotEmpty())
<footer {{ $attributes->merge(['class' => 'space-y-12']) }}>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        @if($likes->isNotEmpty())
        <div class="flex items-center space-x-3">
            <ul class="flex overflow-hidden -space-x-2">
                @foreach($likes->take(6) as $like)
                <li class="block w-8 h-8">
                    <x-avatar
                        :search="parse_url($like->url, PHP_URL_HOST)"
                        :src="$like->author->avatar"
                        width="64"
                        height="64"
                        class="border-2 border-solid border-snow-0 dark:border-night-0"
                        :alt="$like->author->name"
                        :title="$like->author->name"
                    />
                </li>
                @endforeach
            </ul>

            <p class="block flex-grow text-snow-20 dark:text-snow-10">
                <x-icon class="mr-1 fal fa-heart"/>
                {{ $likes->count() }} likes
            </p>
        </div>
        @endif

        @if($reposts->isNotEmpty())
        <div class="flex items-center space-x-3">
            <ul class="flex overflow-hidden -space-x-2">
                @foreach($reposts->take(6) as $repost)
                <li class="block w-8 h-8">
                    <a href="{{ $repost->url }}" class="block" title="{{ $repost->author->name }}">
                        <x-avatar
                            :search="parse_url($repost->url, PHP_URL_HOST)"
                            :src="$repost->author->avatar"
                            width="64"
                            height="64"
                            class="border-2 border-solid border-snow-0 dark:border-night-0"
                            :alt="$repost->author->name"
                        />
                    </a>
                </li>
                @endforeach
            </ul>

            <p class="block flex-grow text-snow-20 dark:text-snow-10">
                <x-icon class="mr-1 fal fa-retweet"/>
                {{ $reposts->count() }} reposts
            </p>
        </div>
        @endif
    </div>

    @if($comments->isNotEmpty())
    <section>
        <h2>Comments</h2>
        <ol class="space-y-8 list-none" reversed>
        @foreach($comments as $comment)
            <li>
                <article>
                    <header class="flex items-center mb-2 space-x-4">
                        <a href="{{ $comment->author->url }}" class="block w-8 h-8" title="{{ $comment->author->name }}">
                            <x-avatar
                                :search="parse_url($comment->url, PHP_URL_HOST)"
                                :src="$comment->author->avatar"
                                width="64"
                                height="64"
                                :alt="$comment->author->name"
                            />
                        </a>
                        <a href="{{ $comment->author->url }}" class="block leading-none hover:text-brand">
                            <strong>{{ $comment->author->name }}</strong>
                        </a>
                        <a href="{{ $comment->url }}" class="block leading-none text-snow-20 dark:text-snow-10 hover:text-brand">
                            <time datetime="{{ $comment->created_at->toIso8601String() }}">
                                {{ $comment->created_at->format('M jS, Y') }}
                            </time>
                        </a>
                    </header>
                    <blockquote>{{ $comment->text }}</blockquote>
                </article>
            </li>
        @endforeach
        </ol>
    </section>
    @endif
</footer>
@endif