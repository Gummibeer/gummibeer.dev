from collections import Counter
from pathlib import Path

POSTS = Path('content/collections/posts')

CATEGORIES = {
    '1990-01-01.alpinejs-responsive-xcloak.md': 'engineering',
    '1990-01-01.composite-rules.md': 'engineering',
    '1990-01-01.feature-tests-are-a-waste-of-time.md': 'engineering',
    '1990-01-01.loading-pivot-data.md': 'engineering',
    '2020-01-01.hello-world.md': 'personal',
    '2020-12-10.human-readable-intervals.md': 'engineering',
    '2020-12-13.telegram-newsletter-command.md': 'engineering',
    '2020-12-17.blade-component-webmentions.md': 'engineering',
    '2020-12-19.phpdoc-in-blade-views.md': 'engineering',
    '2021-01-05.a-recap-of-2020.md': 'personal',
    '2021-01-09.static-search-with-fusejs.md': 'engineering',
    '2021-01-20.font-optimization-with-glyphhanger.md': 'engineering',
    '2021-01-28.yoda.md': 'personal',
    '2022-01-13.end-of-an-era.md': 'personal',
    '2023-08-09.preserving-date-integrity.md': 'engineering',
    '2023-08-14.seamless-role-management-with-discord.md': 'engineering',
    '2023-08-28.carbon-techniques-in-laravel.md': 'engineering',
    '2023-08-30.dynamic-route-model-binding.md': 'engineering',
    '2024-06-25.lessons-from-open-source-software.md': 'professional',
    '2024-06-28.working-at-hospitable.md': 'professional',
    '2024-07-24.geography-in-laravel-part1.md': 'engineering',
    '2026-01-01.blade-components.md': 'engineering',
    '2026-01-01.custom-url-generator.md': 'engineering',
    '2026-01-01.developer-courtesy.md': 'professional',
    '2026-01-01.laravel-translate-array.md': 'engineering',
    '2026-04-24.laravel-custom-email-validation.md': 'engineering',
    '2026-08-01.ai-assisted-site-audit.md': 'engineering',
    '2026-08-01.backend-enums-frontend-copy-sync.md': 'engineering',
    '2026-08-01.chestertons-fence-ui-guards.md': 'engineering',
    '2026-08-01.cross-service-failures.md': 'engineering',
    '2026-08-01.custom-url-generator-frontend-redirects.md': 'engineering',
    '2026-08-01.degoogling-photos-part-1.md': 'personal',
    '2026-08-01.eachbyid-early-exit-laravel-lie.md': 'engineering',
    '2026-08-01.eloquent-huge-related-collections.md': 'engineering',
    '2026-08-01.enums-that-actually-tell-the-truth.md': 'engineering',
    '2026-08-01.events-that-fire-once.md': 'engineering',
    '2026-08-01.fuzzy-name-matching-compliance.md': 'engineering',
    '2026-08-01.gitlab-ci-for-php-packages.md': 'engineering',
    '2026-08-01.http-macro-pending-request.md': 'engineering',
    '2026-08-01.laravel-custom-email-validation.md': 'engineering',
    '2026-08-01.laravel-zero-creative-pipelines.md': 'engineering',
    '2026-08-01.maintainer-burnout-design-problem.md': 'professional',
    '2026-08-01.migrating-statsd-to-sentry-metrics.md': 'engineering',
    '2026-08-01.mining-chats-for-ai-rules.md': 'engineering',
    '2026-08-01.myarchivist-mcp.md': 'engineering',
    '2026-08-01.one-time-operations-in-laravel.md': 'engineering',
    '2026-08-01.personal-operating-system-markdown.md': 'professional',
    '2026-08-01.phpstan-for-laravel-packages.md': 'engineering',
    '2026-08-01.queue-testing-without-footguns.md': 'engineering',
    '2026-08-01.remote-async-communication.md': 'professional',
    '2026-08-01.returning-from-pto.md': 'professional',
    '2026-08-01.shadow-mode-third-party-integration.md': 'engineering',
    '2026-08-01.single-source-of-truth-routing-logic.md': 'engineering',
    '2026-08-01.smart-cropping-php.md': 'engineering',
    '2026-08-01.stale-mr-triage.md': 'professional',
    '2026-08-01.static-analysis-mature-laravel-package.md': 'engineering',
    '2026-08-01.the-ralph-loop.md': 'engineering',
    '2026-08-01.using-cursor-ai-as-staff-engineer.md': 'engineering',
    '2026-08-01.vendor-escalations-that-get-results.md': 'professional',
    '2026-08-01.webhook-replay-when-is-it-safe.md': 'engineering',
    '2026-08-01.writing-agent-skills-that-actually-run.md': 'engineering',
    '2026-08-01.year-of-ai-augmented-engineering.md': 'professional',
    '2026-09-01.ai-session-review-machine.md': 'ttrpg',
    '2026-09-01.archivist-to-notion.md': 'engineering',
    '2026-09-01.constrained-narrative-interpolation.md': 'ttrpg',
    '2026-09-01.lazy-exception-chain.md': 'engineering',
    '2026-09-01.money-values-at-boundaries.md': 'engineering',
    '2026-09-01.phone-number-normalization.md': 'engineering',
    '2026-09-01.typed-vendor-exceptions.md': 'engineering',
}


def migrate(path: Path, category: str) -> None:
    text = path.read_text()
    if not text.startswith('---\n'):
        raise RuntimeError(f'{path} has no YAML frontmatter')

    end = text.find('\n---\n', 4)
    if end == -1:
        raise RuntimeError(f'{path} has unterminated YAML frontmatter')

    frontmatter = text[4:end].splitlines()
    body = text[end + 5:]

    migrated = []
    index = 0
    while index < len(frontmatter):
        line = frontmatter[index]

        if line.startswith('category:'):
            index += 1
            continue

        if line == 'categories:':
            index += 1
            while index < len(frontmatter) and (frontmatter[index].startswith(' ') or not frontmatter[index]):
                index += 1
            continue

        migrated.append(line)
        index += 1

    title_index = next((i for i, line in enumerate(migrated) if line.startswith('title:')), None)
    if title_index is None:
        raise RuntimeError(f'{path} has no title field')

    migrated.insert(title_index + 1, f'category: {category}')
    path.write_text('---\n' + '\n'.join(migrated) + '\n---\n' + body)


actual = {path.name for path in POSTS.glob('*.md')}
expected = set(CATEGORIES)
if actual != expected:
    missing = sorted(actual - expected)
    stale = sorted(expected - actual)
    raise RuntimeError(f'Category mapping mismatch. Missing: {missing}; stale: {stale}')

for filename, category in CATEGORIES.items():
    migrate(POSTS / filename, category)

counts = Counter(CATEGORIES.values())
print(f'Migrated {len(CATEGORIES)} posts: ' + ', '.join(f'{category}={count}' for category, count in sorted(counts.items())))
