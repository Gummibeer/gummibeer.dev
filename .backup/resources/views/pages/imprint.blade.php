<?php /** @var Illuminate\Support\HtmlString $contents */ ?>

@extends('web')

@section('content')
    <x-article class="prose md:prose-lg lg:prose-xl">
        {{ $contents }}
    </x-article>
@endsection