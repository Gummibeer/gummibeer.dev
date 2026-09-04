<?php

namespace App\Http\Middleware;

use App\Http\Responses\MarkdownDataResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Statamic\Facades\Data;
use Symfony\Component\HttpFoundation\Response;

class NegotiateMarkdown
{
    private const AI_BOT_USER_AGENTS = [
        'GPTBot',
        'ClaudeBot',
        'ChatGPT-User',
        'PerplexityBot',
        'Google-Extended',
        'Applebot-Extended',
        'ora-agent',
        'DeepSeekBot',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->isMethod('GET')
            || $request->is('api/*', '*.md')
            || $request->expectsJson()
        ) {
            return $next($request);
        }

        $uri = '/'.trim($request->path(), '/');
        $data = Data::findByUri($uri);

        if (! $data) {
            return $next($request);
        }

        if ($request->wantsMarkdown() || $this->isAiBot($request)) {
            return new MarkdownDataResponse($data);
        }

        $response = $next($request);
        $response->setVary(array_unique([...$response->getVary(), 'Accept', 'Accept-Encoding', 'User-Agent']));

        return $response;
    }

    private function isAiBot(Request $request): bool
    {
        return Str::contains(
            strtolower((string) $request->userAgent()),
            array_map('strtolower', self::AI_BOT_USER_AGENTS),
        );
    }
}
