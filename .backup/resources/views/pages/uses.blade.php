<?php /** @var Illuminate\Support\HtmlString $contents */ ?>

@extends('web')

@push('head')
    <x-og.website/>
@endpush

@section('content')
    <x-article class="prose md:prose-lg lg:prose-xl markdown">
        {{ $contents }}
    </x-article>
@endsection