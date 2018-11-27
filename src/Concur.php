<?php

namespace VdPoel\Concur;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Concur
{
    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * Determine if the given request can access the Horizon dashboard.
     *
     * @param  Model $model
     * @return bool
     */
    public static function check(Model $model)
    {
        return (static::$authUsing ?: function ($model) {
            if ($id = data_get($model, config('concur.migrations.tenancy.foreign_key'))) {
                $owner = app(config('concur.migrations.tenancy.model'))->find($id);

                return $owner->getSetting('concur_enabled', false);
            }

            return false;
        })($model);
    }

    /**
     * Set the callback that should be used to authenticate Horizon users.
     *
     * @param  \Closure $callback
     * @return static
     */
    public static function auth(\Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }
}
