<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\Data;
use Symfony\Component\HttpFoundation\Response;

class AddLinkHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            ! $request->isMethod('GET')
            || $request->is('api/*', '*.md')
            || $request->expectsJson()
        ) {
            return $response;
        }

        $uri = '/'.trim($request->path(), '/');
        $data = Data::findByUri($uri);

        if (! $data) {
            return $response;
        }

        $markdownUrl = $request->path() === '/'
            ? url('/index.md')
            : url('/'.$request->path().'.md');

        $response->headers->set('Link', implode(', ', [
            sprintf('<%s>; rel="sitemap"; type="application/xml"', route('sitemap.xml')),
            sprintf('<%s>; rel="alternate"; type="text/plain"; title="LLMs"', url('/llms.txt')),
            sprintf('<%s>; rel="canonical"; type="text/html"', $data->absoluteUrl()),
            sprintf('<%s>; rel="alternate"; type="text/markdown"; title="Markdown"', $markdownUrl),
        ]));

        return $response;
    }
}
