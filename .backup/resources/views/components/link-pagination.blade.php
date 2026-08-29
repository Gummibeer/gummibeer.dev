<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var App\Services\Paginator $paginator */ ?>

<link rel="first" href="{{ $paginator->url(1) }}">
@unless ($paginator->onFirstPage())
    <link rel="prev" href="{{ $paginator->previousPageUrl() }}">
@endunless
@if($paginator->hasMorePages())
    <link rel="next" href="{{ $paginator->nextPageUrl() }}">
@endif
<link rel="last" href="{{ $paginator->url($paginator->lastPage()) }}">