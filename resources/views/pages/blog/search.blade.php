@extends ('web')

@push ('head')
    <x-og.website />
    <x-link-feed route="blog.feed" />
@endpush

@section ('content')
    <x-section>
        <h1 class="mb-8 text-6xl leading-none font-black text-night-0 dark:text-white">Search</h1>
        <x-post.search />

        @if (request()->filled('q'))
            <statamic:search:results
                index="blog"
                as="results"
            >
                @forelse ($results as $result)
                    @if ($loop->first)
                        <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
                    @endif

                    <x-post.preview :post="$result" />

                    @if ($loop->last)
                        </div>
                    @endif
                @empty
                    <p class="text-lg">No posts found for &ldquo;{{ request('q') }}&rdquo;.</p>
                @endforelse
            </statamic:search:results>
        @endif
    </x-section>
@endsection
