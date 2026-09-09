<?php

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Nonce\RandomString;
use Spatie\Csp\Presets\CloudflareWebAnalytics;
use Spatie\Csp\Value;

return [
    'presets' => [],

    'directives' => [
        [Directive::DEFAULT, Keyword::NONE],
        [Directive::SCRIPT, [Keyword::SELF, 'https://u.gummibeer.dev']],
        [Directive::SCRIPT_ATTR, Keyword::NONE],
        [Directive::STYLE, Keyword::SELF],
        [Directive::STYLE_ATTR, Keyword::NONE],
        [Directive::IMG, [Keyword::SELF, 'https://cdn.jsdelivr.net']],
        [Directive::FONT, Keyword::SELF],
        [Directive::CONNECT, [Keyword::SELF, 'https://u.gummibeer.dev']],
        [Directive::OBJECT, Keyword::NONE],
        [Directive::FRAME, Keyword::NONE],
        [Directive::WORKER, Keyword::NONE],
        [Directive::MEDIA, Keyword::NONE],
        [Directive::MANIFEST, Keyword::NONE],
        [Directive::BASE, Keyword::NONE],
        [Directive::FORM_ACTION, Keyword::SELF],
        [Directive::FRAME_ANCESTORS, Keyword::NONE],
        [Directive::UPGRADE_INSECURE_REQUESTS, Value::NO_VALUE],
    ],

    'report_only_presets' => [],
    'report_only_directives' => [],
    'report_uri' => '',
    'report_only_uri' => '',
    'report_to' => '',
    'report_only_to' => '',
    'reporting_endpoints' => [],
    'enabled' => true,
    'enabled_while_hot_reloading' => false,
    'nonce_generator' => RandomString::class,
    'nonce_enabled' => false,
];
