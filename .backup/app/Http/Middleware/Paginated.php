<?php

namespace App\Http\Middleware;

use Closure;

class Paginated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        preg_match('/'.$request->route()->wheres['page'].'/', $request->route('page'), $hits);
        $request->route()->setParameter('page', intval($hits[1] ?? 1));

        return $next($request);
    }
}
