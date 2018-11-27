<?php

namespace VdPoel\Concur\Observers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Cache\Store;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Concur;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;

class AuthenticatableObserver
{
    /**
     * @var int
     */
    protected const CACHE_LIFETIME = 5;

    /**
     * @var Factory
     */
    protected $concur;

    /**
     * @var Store
     */
    protected $cache;

    /**
     * AuthenticatableObserver constructor.
     */
    public function __construct()
    {
        $this->concur = app(Factory::class);
        $this->cache = app('cache')->getStore();
    }

    /**
     * Handle the Model "creating" event.
     *
     * @param  Authenticatable|Model $model
     * @return void
     */
    public function creating($model)
    {
        if (Concur::check($model)) {
            $this->cache->put($this->getCacheKey($model), encrypt(request()->input('password')), static::CACHE_LIFETIME);
        }
    }

    /**
     * Handle the Model "created" event.
     *
     * @param  Authenticatable|Model $model
     * @return void
     */
    public function created($model)
    {
        if (Concur::check($model)) {
            event(LookupTravelProfile::class, $model);
        }
    }

    /**
     * Handle the Model "updated" event.
     *
     * @param  $model
     * @return void
     */
    public function updated($model)
    {
        //
    }

    /**
     * Handle the Model "deleted" event.
     *
     * @param  $model
     * @return void
     */
    public function deleted($model)
    {
        //
    }

    /**
     * @param Authenticatable|Model $model
     * @return string
     */
    protected function getCacheKey ($model): string
    {
        return md5(implode('.', $model->only(['first_name', 'last_name', 'email', 'event_id'])));
    }
}
