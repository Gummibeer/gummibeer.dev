<?php

namespace App\Http\Responses;

use Illuminate\Support\Stringable;
use Illuminate\Support\Str;
use Statamic\Http\Responses\DataResponse;

class MarkdownDataResponse extends DataResponse
{
    protected function contents(): string
    {
        $description = Str::of((string) $this->data->value('description'))->trim();
        $markdown = Str::of((string) $this->data->value('content'))
            ->trim()
            ->unless(
                fn (Stringable $content): bool => $content->startsWith('# '),
                fn (Stringable $content): Stringable => Str::of((string) $this->data->title())
                    ->trim()
                    ->prepend('# ')
                    ->when(
                        $description->isNotEmpty(),
                        fn (Stringable $markdown): Stringable => $markdown->append(PHP_EOL.PHP_EOL, $description),
                    )
                    ->when(
                        $content->isNotEmpty(),
                        fn (Stringable $markdown): Stringable => $markdown->append(PHP_EOL.PHP_EOL, $content),
                    ),
            );
        $frontmatter = [
            '---',
            'title: '.$this->yamlScalar((string) $this->data->title()),
        ];

        if ($description->isNotEmpty()) {
            $frontmatter[] = 'description: '.$this->yamlScalar($description->toString());
        }

        $frontmatter[] = 'canonical: '.$this->yamlScalar($this->data->absoluteUrl());
        $frontmatter[] = '---';

        return Str::of(implode(PHP_EOL, $frontmatter))
            ->append(PHP_EOL.PHP_EOL, $markdown, PHP_EOL)
            ->toString();
    }

    protected function adjustResponseType(): static
    {
        $this->headers['Content-Type'] = 'text/markdown; charset=UTF-8';

        return $this;
    }

    protected function addContentHeaders(): static
    {
        parent::addContentHeaders();

        $this->headers['Content-Type'] = 'text/markdown; charset=UTF-8';
        $this->headers['Link'] = sprintf(
            '<%s>; rel="canonical"; type="text/html"',
            $this->data->absoluteUrl(),
        );
        $vary = array_filter(array_map(
            trim(...),
            explode(',', (string) ($this->headers['Vary'] ?? '')),
        ));
        $this->headers['Vary'] = implode(', ', array_unique([
            ...$vary,
            'Accept',
            'Accept-Encoding',
            'User-Agent',
        ]));

        return $this;
    }

    private function yamlScalar(string $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );
    }
}
