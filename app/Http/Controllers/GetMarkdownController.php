<?php

namespace App\Http\Controllers;

use App\Http\Responses\MarkdownDataResponse;
use Statamic\Facades\Data;

class GetMarkdownController
{
    public function __invoke(string $uri): MarkdownDataResponse
    {
        $uri = $uri === 'index'
            ? '/'
            : '/'.trim($uri, '/');

        abort_unless($data = Data::findByUri($uri), 404);

        return new MarkdownDataResponse($data);
    }
}
