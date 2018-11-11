<?php

namespace VdPoel\Concur;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use VdPoel\Concur\Api\Factory;

/**
 * Class ConcurGuard
 * @package VdPoel\Concur
 */
class ConcurGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var Factory
     */
    protected $concur;

    /**
     * @var UserProvider
     */
    protected $provider;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Authenticatable
     */
    protected $user;

    /**
     * ConcurGuard constructor.
     * @param Factory $concur
     * @param UserProvider $provider
     * @param Request $request
     */
    public function __construct(Factory $concur, UserProvider $provider, Request $request)
    {
        $this->concur   = $concur;
        $this->provider = $provider;
        $this->request  = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return (bool)$this->attempt($credentials);
    }

    /**
     * Attempt to authenticate the user using the given credentials.
     *
     * @param  array $credentials
     *
     * @return bool|string
     */
    public function attempt(array $credentials = [])
    {
        try {
            return $this->concur->user->get($credentials);
        } catch (ClientException $exception) {
            return false;
        }
    }

    /**
     * Attempt to register a new user account.
     *
     * @param  array $credentials
     *
     * @return bool|string
     * @throws GuzzleException
     */
    public function register(array $credentials = [])
    {
        return $this->concur->travelProfile()->create($credentials);
    }

    /**
     * Attempt to register a new user account.
     *
     * @param  array $credentials
     *
     * @return bool|string
     * @throws GuzzleException
     */
    public function profile(array $credentials = [])
    {
        return $this->concur->travelProfile->create($credentials);
    }
}
