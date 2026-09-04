<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Statamic\Facades\User;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginStatamicControlPanel
{
    private const EMAIL = 'dev@gummibeer.de';

    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->isLocal() || ! $this->isControlPanelRequest($request) || Auth::check()) {
            return $next($request);
        }

        $user = User::findByEmail(self::EMAIL);

        if (! $user) {
            throw new RuntimeException('The local Statamic user is missing.');
        }

        Auth::login($user);

        return $next($request);
    }

    private function isControlPanelRequest(Request $request): bool
    {
        $route = trim((string) config('statamic.cp.route', 'cp'), '/');

        return $request->is($route, $route.'/*');
    }
}
