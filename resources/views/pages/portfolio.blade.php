@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl"> {!! $content !!} </x-article>

    <x-portfolio.projects />
@endsection
