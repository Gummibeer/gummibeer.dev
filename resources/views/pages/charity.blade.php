<?php /** @var Illuminate\Support\HtmlString $contents */ ?>
<?php /** @var Illuminate\Support\Collection<int, Statamic\Entries\Entry> $charities */ ?>

@extends ('web')

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl"> {!! $contents !!} </x-article>

    <x-section class="bg-dotted">
        <x-grid class="xl:grid-cols-4">
            @foreach ($charities as $charity)
                <div class="overflow-hidden rounded-4 bg-white shadow dark:bg-night-20">
                    <a
                        href="{{ $charity->value('url') }}"
                        target="_blank"
                        rel="noreferrer noopener"
                        class="block pb-1"
                    >
                        <x-figure>
                            <x-img
                                :src="$charity->image"
                                width="768"
                                ratio="16:9"
                                :alt="$charity->value('title')"
                            />
                            <x-slot name="caption">
                                {{ $charity->value('title') }}
                            </x-slot>
                        </x-figure>
                    </a>
                </div>
            @endforeach
        </x-grid>
    </x-section>
@endsection
