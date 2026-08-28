---
id: d87b93b2-7564-4f3c-bec1-a056e7dcaf7b
blueprint: draft
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
