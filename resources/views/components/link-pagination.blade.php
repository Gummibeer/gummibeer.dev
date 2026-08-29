<link
    rel="first"
    href="{{ $paginate['links']['all'][0]['url'] }}"
/>
@if ($paginate['prev_page'])
    <link
        rel="prev"
        href="{{ $paginate['prev_page'] }}"
    />
@endif
@if ($paginate['next_page'])
    <link
        rel="next"
        href="{{ $paginate['next_page'] }}"
    />
@endif
<link
    rel="last"
    href="{{ $paginate['links']['all'][$paginate['total_pages'] - 1]['url'] }}"
/>
