<?php

namespace App\Http\Responses;

use Statamic\Http\Responses\DataResponse;

class MarkdownDataResponse extends DataResponse
{
    protected function contents(): string
    {
        $content = trim((string) $this->data->value('content'));

        if (preg_match('/^#\s+\S/', $content) === 1) {
            return $content.PHP_EOL;
        }

        $markdown = ['# '.trim((string) $this->data->title())];
        $description = trim((string) $this->data->value('description'));

        if ($description !== '') {
            $markdown[] = $description;
        }

        if ($content !== '') {
            $markdown[] = $content;
        }

        return implode(PHP_EOL.PHP_EOL, $markdown).PHP_EOL;
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
