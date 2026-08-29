<?php /** @var Illuminate\Support\HtmlString $contents */ ?>

@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl"> {!! $contents !!} </x-article>

    <x-portfolio.projects />
@endsection
