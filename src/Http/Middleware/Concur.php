<?php

namespace VdPoel\Concur\Http\Middleware;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use VdPoel\Concur\Api\Factory;

/**
 * Class Concur
 *
 * @package VdPoel\Concur\Http\Middleware
 */
class Concur
{
    /**
     * @var Factory
     */
    protected $concur;

    /**
     * Create a new BaseMiddleware instance.
     *
     * @param Factory $concur
     */
    public function __construct(Factory $concur)
    {
        $this->concur = $concur;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws AuthenticationException
     * @throws GuzzleException
     */
    public function handle(Request $request, \Closure $next)
    {
        abort_unless($this->concur->authentication->login(), 401, 'Unauthorized.');

        return $next($request);
    }
}
