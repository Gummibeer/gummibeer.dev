@php ($route = app('router')->getRoutes()->getByName($route))

<link
    rel="alternate"
    type="application/rss+xml"
    href="{{ url()->toRoute($route, array_merge($parameters ?? [], ['format' => 'rss']), true) }}"
/>
<link
    rel="alternate"
    type="application/atom+xml"
    href="{{ url()->toRoute($route, array_merge($parameters ?? [], ['format' => 'atom']), true) }}"
/>
