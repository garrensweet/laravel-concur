<?php

namespace VdPoel\Concur\Api;

/**
 * Class Factory
 * @package VdPoel\Concur
 * @property Authentication $authentication
 * @property TravelProfile $travelProfile
 * @property User $user
 */
class Factory
{
    /**
     * @return Authentication
     */
    public function authentication(): Authentication
    {
        return app()->make('concur.api.authentication');
    }

    /**
     * @return TravelProfile
     */
    public function travelProfile(): TravelProfile
    {
        return app()->make('concur.api.travel.profile');
    }

    /**
     * @return User
     */
    public function user(): User
    {
        return app()->make('concur.api.user');
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return call_user_func([$this, $name]);
        }

        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        throw new \InvalidArgumentException(sprintf('Property or method %s does not exist.', $name), 400);
    }
}
