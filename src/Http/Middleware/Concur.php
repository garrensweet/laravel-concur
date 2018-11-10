<?php

namespace VdPoel\Concur\Http\Middleware;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use VdPoel\Concur\Api\Authentication;

/**
 * Class BaseMiddleware
 * @package VdPoel\Concur\Http\Middleware
 */
class Concur
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * Create a new BaseMiddleware instance.
     *
     * @param Authentication $auth
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($this->auth->login()) {
            return $next($request);
        }

        abort(401, 'Unauthorized.');
    }
}
