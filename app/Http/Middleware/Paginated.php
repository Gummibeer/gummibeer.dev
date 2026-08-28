<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Paginated
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        preg_match('/'.$request->route()->wheres['page'].'/', $request->route('page'), $hits);
        $request->route()->setParameter('page', intval($hits[1] ?? 1));

        return $next($request);
    }
}
