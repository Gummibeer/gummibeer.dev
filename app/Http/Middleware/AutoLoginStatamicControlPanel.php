<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Statamic\Facades\User;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginStatamicControlPanel
{
    private const EMAIL = 'local@gummibeer.dev';

    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->isLocal() || ! $this->isControlPanelRequest($request) || Auth::check()) {
            return $next($request);
        }

        $user = User::findByEmail(self::EMAIL);

        if (! $user) {
            $user = User::make()
                ->email(self::EMAIL)
                ->makeSuper();

            $user->save();
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
