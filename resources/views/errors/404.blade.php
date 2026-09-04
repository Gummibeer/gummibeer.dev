@extends ('web')

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl">
        <h1>Not Found</h1>

        <p>The page you've entered isn't available. Please verify that it's written correctly.</p>

        <p>The URL you've opened is:</p>

        <pre
            x-data
            x-cloak
        ><code x-text="window.location"></code></pre>

        <p>If the URL is correct, these should get you back on track:</p>

        <ul>
            <li><a href="{{ url('/') }}">home page</a></li>
            <li><a href="{{ route('sitemap.xml') }}">sitemap</a></li>
            <li><a href="{{ url('/llms.txt') }}">llms.txt</a></li>
        </ul>
    </x-article>
@endsection
