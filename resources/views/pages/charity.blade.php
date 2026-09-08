@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl max-w-none"> {!! $content !!} </x-article>

    <x-charity.organizations />
@endsection
