<?php /** @var Statamic\Taxonomies\LocalizedTerm $page */ ?>

@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-category.entries :category="$page" />
@endsection
