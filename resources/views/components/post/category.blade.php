<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Statamic\Taxonomies\LocalizedTerm $category */ ?>

<strong {{ $attributes->except('category')->merge(['class' => 'uppercase']) }}>
    <a
        href="{{ $category->url }}"
        class="text-brand"
    >
        {{ $category->title }}
    </a>
</strong>
