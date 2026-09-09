<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Csp\AddCspHeaders;
use Symfony\Component\HttpFoundation\Response;

class AddContentSecurityPolicyHeaders extends AddCspHeaders
{
    public function handle(Request $request, Closure $next, ?string $customPreset = null): Response
    {
        $controlPanelRoute = trim((string) config('statamic.cp.route'), '/');

        if (
            config('statamic.cp.enabled')
            && $controlPanelRoute !== ''
            && $request->is($controlPanelRoute, $controlPanelRoute.'/*')
        ) {
            return $next($request);
        }

        return parent::handle($request, $next, $customPreset);
    }
}
