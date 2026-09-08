<?php /** @var Statamic\Contracts\Entries\Entry $page */ ?>

@extends ('web')

@push ('head')
    <link
        rel="index"
        href="{{ $page->collection()->url() }}"
    />
    <x-og.website />
@endpush

@section ('content')
    <x-article class="markdown">
        <header class="mb-8">
            <a
                href="{{ $page->video }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                <x-img
                    :src="$page->image"
                    width="1536"
                    ratio="16:9"
                    :crop="true"
                    :alt="$page->title"
                    class="mb-8 rounded-4"
                />
            </a>
            @if ($page->category)
                <x-post.category
                    :category="$page->category"
                    class="mb-4"
                />
            @endif
            <x-stream.aside :stream="$page" />
        </header>
        <main class="prose max-w-none md:prose-lg lg:prose-xl">
            <h1>{{ $page->title }}</h1>

            @if ($page->channel_name)
                <p>
                    Streamed by
                    <a
                        href="{{ $page->channel_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >{{ $page->channel_name }}</a>.
                </p>
            @endif

            @if ($page->description)
                <section>
                    <h2>Description</h2>
                    <div class="whitespace-pre-wrap">{{ $page->description }}</div>
                </section>
            @endif

            @if ($page->content)
                {!! $page->content !!}
            @endif

            <p>
                <a
                    href="{{ $page->video }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >Watch on YouTube</a>
            </p>
        </main>

        @if ($page->transcript_text)
            <footer class="mt-16">
                <x-text-file
                    :id="'transcript-'.$page->slug"
                    title="Transcript"
                    :content="$page->transcript_text"
                    :path="'/'.ltrim($page->transcript, '/')"
                    copy-label="copy transcript to clipboard"
                    download-label="download transcript"
                />
            </footer>
        @endif
    </x-article>
@endsection
