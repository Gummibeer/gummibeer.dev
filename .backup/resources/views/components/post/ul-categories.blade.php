<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var App\Post $post */ ?>

<ul {{ $attributes->except('post')->merge(['class' => 'flex list-none space-x-4 uppercase']) }}>
    @foreach($post->categories() as $category)
        <li>
            <strong>
                <a href="{{ $category->url }}" class="text-brand">
                    {{ $category->title }}
                </a>
            </strong>
        </li>
    @endforeach
</ul>