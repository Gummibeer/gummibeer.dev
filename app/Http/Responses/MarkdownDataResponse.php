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

        return Str::of((string) $this->data->value('content'))
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
            )
            ->append(PHP_EOL)
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

        return $this;
    }
}
