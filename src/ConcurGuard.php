<?php

namespace VdPoel\Concur;

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
     * @param Request $request
     * @return ConcurGuard
     */
    public function setRequest(Request $request): ConcurGuard
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return UserProvider
     */
    public function getProvider(): UserProvider
    {
        return $this->provider;
    }

    /**
     * Set the user provider used by the guard.
     *
     * @param  UserProvider $provider
     *
     * @return $this
     */
    public function setProvider(UserProvider $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     * @throws GuzzleException
     */
    public function validate(array $credentials = [])
    {
        return (bool)$this->attempt($credentials);
    }

    /**
     * Attempt to authenticate the user using the given credentials.
     *
     * @param  array $credentials
     * @param  bool $login
     *
     * @return bool|string
     * @throws GuzzleException
     */
    public function attempt(array $credentials = [], $login = true)
    {
        switch (true) {
            case array_has($credentials, 'LoginID');
                return $this->concur->user->get($credentials);
                break;
            case array_has($credentials, 'email');
                return $this->concur->user->get(['LoginID' => array_get($credentials, 'email')]);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Missing LoginID or email address.'));
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
        return $this->concur->travelProfile->create($credentials);
    }
}
