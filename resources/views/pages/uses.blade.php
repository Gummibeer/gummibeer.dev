@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-article class="prose max-w-none md:prose-lg lg:prose-xl"> {!! $content !!} </x-article>
@endsection
