---
is_draft: true
---

```php
// dashboard.blade.php
Arr::random(trans('dashboard.welcome', ['name' => Auth::user()->firstname]));

// lang/de/dashboard.php
return [
	'welcome' => [
        'Hallo :name',
        'Moin :name',
        'Hey :name',
    ],
];
```
