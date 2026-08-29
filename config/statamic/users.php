<?php

return [
    'repository' => 'file',

    'repositories' => [
        'file' => [
            'driver' => 'file',
            'paths' => [
                'roles' => resource_path('users/roles.yaml'),
                'groups' => resource_path('users/groups.yaml'),
            ],
        ],
        'eloquent' => [
            'driver' => 'eloquent',
        ],
    ],

    'avatars' => 'initials',
    'new_user_roles' => [],
    'new_user_groups' => [],
    'registration_form_honeypot_field' => null,
    'wizard_invitation' => true,

    'passwords' => [
        'resets' => 'users',
        'activations' => 'activations',
    ],

    'database' => config('database.default'),

    'tables' => [
        'users' => 'users',
        'role_user' => 'role_user',
        'roles' => false,
        'group_user' => 'group_user',
        'groups' => false,
        'webauthn' => 'webauthn',
    ],

    'guards' => [
        'cp' => 'web',
        'web' => 'web',
    ],

    'impersonate' => [
        'enabled' => env('STATAMIC_IMPERSONATE_ENABLED', true),
        'redirect' => env('STATAMIC_IMPERSONATE_REDIRECT'),
    ],

    'elevated_sessions_enabled' => env('STATAMIC_ELEVATED_SESSIONS_ENABLED', false),
    'elevated_session_duration' => 15,
    'elevated_sessions_url' => null,
    'two_factor_enabled' => env('STATAMIC_TWO_FACTOR_ENABLED', false),
    'two_factor_enforced_roles' => [],
    'two_factor_challenge_url' => null,
    'two_factor_setup_url' => null,
    'sort_field' => 'email',
    'sort_direction' => 'asc',
];
