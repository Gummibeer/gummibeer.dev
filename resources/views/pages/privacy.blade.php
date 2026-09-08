@extends ('web')

@section ('content')
    <x-article class="prose max-w-none md:prose-lg lg:prose-xl"> {!! $content !!} </x-article>
@endsection
