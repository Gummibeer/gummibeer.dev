@props(['post'])

@php
    $comments = \Statamic\Facades\Entry::query()
        ->where('collection', 'comments')
        ->whereStatus('published')
        ->where('post', $post->id())
        ->orderBy('date', 'asc')
        ->get();
@endphp

<section
    class="mt-16 border-t border-night-10 pt-8"
    x-data="comments(@js(route('comments.form', $post->id())), @js(config('services.turnstile.site_key')))"
>
    <h2 class="mb-6 text-2xl font-bold">Comments</h2>

    @if ($comments->isNotEmpty())
        <div class="mb-8 space-y-6">
            @foreach ($comments as $comment)
                <article>
                    <header class="mb-2 flex flex-wrap items-baseline gap-x-2 text-sm">
                        <strong>{{ $comment->value('name') }}</strong>
                        <time datetime="{{ $comment->date()->toAtomString() }}" class="text-night-60">
                            {{ $comment->date()->format('M j, Y') }}
                        </time>
                    </header>
                    <div class="whitespace-pre-line">{{ $comment->value('comment') }}</div>
                </article>
            @endforeach
        </div>
    @endif

    <div x-show="!loaded && !success">
        <button
            type="button"
            class="rounded-1 bg-brand px-4 py-2 font-bold text-night-0 shadow transition hover:brightness-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-50"
            @click="load"
            :disabled="loading"
        >
            <span x-show="!loading">Leave a comment</span>
            <span x-show="loading" x-cloak>Loading…</span>
        </button>
    </div>

    <div x-ref="form" x-show="loaded && !success" x-cloak></div>

    <p x-show="success" x-cloak class="font-bold">
        Thanks! Your comment is waiting for approval.
    </p>

    <p x-show="error && !loaded" x-cloak class="mt-3 text-sm font-bold" x-text="error"></p>
</section>
