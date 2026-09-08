<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Statamic\Contracts\Taxonomies\Term $category */ ?>

<strong {{ $attributes->except('category')->merge(['class' => 'uppercase']) }}>
    <a
        href="{{ url('/category/'.$category->slug) }}"
        class="text-brand"
    >
        {{ $category->title }}
    </a>
</strong>
