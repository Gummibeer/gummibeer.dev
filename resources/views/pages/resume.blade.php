@extends ('web')

@push ('head')
    <x-og.profile />
@endpush

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl"> {!! $content !!} </x-article>

    <x-resume.jobs />
    <x-resume.hacktoberfest />
@endsection
