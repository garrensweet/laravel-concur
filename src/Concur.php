<?php

namespace VdPoel\Concur;

use Illuminate\Contracts\Foundation\Application;
use VdPoel\Concur\Api\Authentication;
use VdPoel\Concur\Api\TravelProfile;
use VdPoel\Concur\Api\User;

/**
 * Class Concur
 * @package VdPoel\Concur
 * @property Authentication $auth
 * @property TravelProfile $travelProfile
 * @property User $user
 */
class Concur
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * ConcurClient constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Authentication
     */
    public function auth(): Authentication
    {
        return $this->app->make(Authentication::class);
    }

    /**
     * @return TravelProfile
     */
    public function travelProfile(): TravelProfile
    {
        return $this->app->make(TravelProfile::class);
    }

    /**
     * @return User
     */
    public function user(): User
    {
        return $this->app->make(User::class);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}();
        }

        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        throw new \InvalidArgumentException(sprintf('No method or property named %s exists.', $name));
    }
}
