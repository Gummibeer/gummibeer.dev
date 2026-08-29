@extends('web')

@section('content')
<x-article class="prose md:prose-lg lg:prose-xl">
    <h1>Not Found</h1>

    <p>
        The page you've entered isn't available.
        Please verify that it's written correctly.
    </p>

    <p>
        The URL you've opened is:
    </p>

    <pre x-data x-cloak><code x-text="window.location"></code></pre>

    <p>
        If you think that the page should exist don't hesitate to <a href="https://twitter.com/devgummibeer">contact me</a>.
    </p>

    <a href="{{ url('/') }}">back to home page</a>
</x-article>
@endsection