<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Preset
    |--------------------------------------------------------------------------
    |
    | Start with a preset, then use 'rules' below to turn individual rules off,
    | change their severity, or set rule-specific options where appropriate.
    |
    | Available presets:
    |  - 'recommended': rules that catch defects, quiet on correct Blade
    |  - 'strict':      'recommended' plus context-sensitive and opinionated rules
    |  - 'stylistic':   formatting and syntax preferences, no defects
    |  - 'migration':   one-off rewrites onto current Blade syntax; run it from
    |                   the command line rather than leaving it switched on here
    |  - 'empty':       no rules; list your own below
    |
    | Style is a separate axis from correctness, so combine presets freely:
    |
    |     'preset' => ['recommended', 'stylistic'],
    |
    | Naming a preset also means rules added in future Sheath releases are
    | picked up automatically instead of silently never running.
    |
    */

    'preset' => 'recommended',

    /*
    |--------------------------------------------------------------------------
    | Lint Paths
    |--------------------------------------------------------------------------
    |
    | Specify which directories or files Sheath should lint by default whenever
    | you run 'sheath:lint' without passing any file or directory arguments.
    |
    */

    'paths' => [
        'resources/views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore Patterns
    |--------------------------------------------------------------------------
    |
    | List file patterns that Sheath should exclude from linting; standard glob
    | patterns such as 'vendor/**' and '*.min.blade.php' are supported here.
    |
    */

    'ignore' => [
        'vendor/**',
        'node_modules/**',
        'storage/**',
        'resources/views/emails/**',
    ],

    /*
    |--------------------------------------------------------------------------
    | Component Semantic Mappings
    |--------------------------------------------------------------------------
    |
    | Tell HTML and accessibility rules which native element each local Blade
    | component renders as. These mappings are explicit and analysis-only;
    | Sheath never runs components or applies native-tag fixes to them.
    |
    */

    'componentMappings' => [
        // 'x-button' => 'button',
        // 'x-navigation.link' => 'a',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rule Overrides
    |--------------------------------------------------------------------------
    |
    | Adjust individual rules on top of your chosen preset.
    | Severity values: 'off', 'info', 'warning', 'error'
    |
    | You can also use the array format for rules with options:
    | 'rule-id' => ['error', ['option' => 'value']],
    |
    | Run 'php artisan sheath:lint --print-config' to see the resolved result.
    |
    */

    'rules' => [
        // 'security-no-raw-echo' => ['error', ['allowed' => ['$post->renderedBody']]],
        // 'security-csrf-field' => ['error', ['applicationHosts' => ['admin.example']]],
        // 'best-practices-self-closing-void-elements' => 'off',
        'a11y-list-semantics' => 'off',
        'best-practices-require-li-container' => 'off',
        'best-practices-require-doctype' => 'off',
        'best-practices-self-closing-void-elements' => 'off',
    ],

    /*
    |--------------------------------------------------------------------------
    | Never Fix
    |--------------------------------------------------------------------------
    |
    | Rules listed here still report violations, but Sheath never offers or applies
    | fixes. Use this option when a given rule's fix does not suit your codebase
    | but you still want to see the findings. Entries can identify rules with
    | rule IDs or fully qualified RuleClass::class values, as appropriate.
    |
    */

    'neverFix' => [
        // 'best-practices-no-inline-styles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Baseline Line Tolerance
    |--------------------------------------------------------------------------
    |
    | Set how many lines a violation may drift and still match its baseline entry
    | when unrelated edits move the location of an existing baselined finding.
    |
    */

    'baselineLineTolerance' => 3,

    /*
    |--------------------------------------------------------------------------
    | Inline Suppressions
    |--------------------------------------------------------------------------
    |
    | Whether 'sheath-disable' comments in a template are honoured:
    |
    |     {{-- sheath-disable-next-line security-no-raw-echo --}}
    |     {!! $post->renderedBody !!}
    |
    | Set this to false, or pass --no-inline-config, to make a run authoritative
    | so a template cannot silence its own findings. Useful in CI.
    |
    */

    'inlineSuppressions' => true,

    /*
    |--------------------------------------------------------------------------
    | Package Requirement Mode
    |--------------------------------------------------------------------------
    |
    | How to handle rules that declare package requirements via #[RequiresPackage].
    |
    | Options:
    |  - 'skip': Don't register rules whose package requirements aren't met
    |  - 'disable': Register but don't run rules whose requirements aren't met
    |  - 'ignore': Ignore package requirements entirely (run all rules)
    |
    */

    'packageRequirementMode' => 'skip',

];
