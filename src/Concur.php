<?php

namespace VdPoel\Concur;

use Illuminate\Http\Request;

class Concur
{
    /**
     * The callback that should be used to authenticate Concur users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * Set the callback that should be used to authenticate Concur users.
     *
     * @param  \Closure $callback
     * @return static
     */
    public static function auth(\Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Determine if the given request can access the Concur dashboard.
     *
     * @return bool
     */
    public static function check()
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })();
    }
}
