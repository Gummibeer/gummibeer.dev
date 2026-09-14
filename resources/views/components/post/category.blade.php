<strong {{ $attributes->except('category')->merge(['class' => 'uppercase']) }}>
    <a
        href="{{ url('/category/'.$category->slug) }}"
        class="text-brand"
    >
        {{ $category->title }}
    </a>
</strong>
