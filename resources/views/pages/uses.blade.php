@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-article class="markdown prose md:prose-lg lg:prose-xl"> {!! $content !!} </x-article>
@endsection
