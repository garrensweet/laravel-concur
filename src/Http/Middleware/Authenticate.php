<?php

namespace VdPoel\Concur\Http\Middleware;

use Illuminate\Http\Request;
use VdPoel\Concur\Concur;

/**
 * Class Authenticate
 *
 * @package VdPoel\Concur\Http\Middleware
 */
class Authenticate
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        return Concur::check() ? $next($request) : abort(403);
    }
}
