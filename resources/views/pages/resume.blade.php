@extends ('web')

@section ('content')
    <x-article class="prose max-w-none md:prose-lg lg:prose-xl"> {!! $content !!} </x-article>

    <x-resume.jobs />
    <x-resume.hacktoberfest />
@endsection
