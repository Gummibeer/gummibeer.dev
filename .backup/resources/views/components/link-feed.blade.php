<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var array $parameters */ ?>
<?php /** @var string|Illuminate\Routing\Route $route */ ?>

@php($route = app('router')->getRoutes()->getByName($route))

<link rel="alternate" type="application/rss+xml" href="{{ url()->toRoute($route, array_merge($parameters ?? [], ['format' => 'rss']), true) }}">
<link rel="alternate" type="application/atom+xml" href="{{ url()->toRoute($route, array_merge($parameters ?? [], ['format' => 'atom']), true) }}">