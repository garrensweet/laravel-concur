<?php

namespace VdPoel\Concur\Events\Subscribers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Events\TravelProfile\CreateTravelProfile;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;
use VdPoel\Concur\Events\TravelProfile\TravelProfileCreated;
use VdPoel\Concur\Events\TravelProfile\TravelProfileFound;
use VdPoel\Concur\Events\TravelProfile\TravelProfileNotFound;

class TravelProfileEventSubscriber
{
    /**
     * @var Factory
     */
    protected $concur;

    /**
     * TravelProfileEventSubscriber constructor.
     *
     * @param Factory $concur
     */
    public function __construct(Factory $concur)
    {
        $this->concur = $concur;
    }

    /**
     * @param Authenticatable|Model $model
     */
    public function create($model)
    {
        try {
            $attributes = $model->only(array_values(config('concur.form_params.travel.profile')));

            $params = array_combine(array_keys(config('concur.form_params.travel.profile')), $attributes);

            $this->concur->travelProfile->create($params);
        } catch (ClientException $exception) {
            logger($exception->getMessage());
        } catch (GuzzleException $exception) {
            logger($exception->getMessage());
        }
    }

    /**
     * @param Authenticatable|Model $model
     * @throws GuzzleException
     */
    public function lookup($model)
    {
        $this->concur->travelProfile->get(['userid_value' => $model->getAttribute('email')]);
    }

    /**
     * @param Authenticatable|Model $model
     * @throws GuzzleException
     */
    public function profileNotFound($model)
    {
        $attributes = $model->only(array_values(config('concur.form_params.travel.profile')));

        $params = array_combine(array_keys(config('concur.form_params.travel.profile')), $attributes);

        $key = app()->makeWith('concur.cache.key', compact('model'));

        $encryptedPassword = app('cache')->get($key);

        $response = $this->concur->travelProfile->create(array_merge($params, [
            'Password' => decrypt($encryptedPassword)
        ]));

        $xml = simplexml_load_string($response->getBody()->getContents());

        dump($xml);
    }

    /**
     * @param mixed $model
     * @throws GuzzleException
     */
    public function profileFound($model)
    {
        $attributes = $model->only(array_values(config('concur.form_params.travel.profile')));

        $params = array_combine(array_keys(config('concur.form_params.travel.profile')), $attributes);

        $key = app()->makeWith('concur.cache.key', compact('model'));

        $encryptedPassword = app('cache')->get($key);

        $response = $this->concur->travelProfile->create(array_merge($params, [
            'Password' => decrypt($encryptedPassword)
        ]));

        $xml = simplexml_load_string($response->getBody()->getContents());

        dump($xml);
    }

    /**
     * @param mixed $payload
     */
    public function profileCreated($payload)
    {
        dump($payload);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(CreateTravelProfile::class, sprintf('%s@create', static::class));
        $events->listen(LookupTravelProfile::class, sprintf('%s@lookup', static::class));
        $events->listen(TravelProfileFound::class, sprintf('%s@profileFound', static::class));
        $events->listen(TravelProfileNotFound::class, sprintf('%s@profileNotFound', static::class));
        $events->listen(TravelProfileCreated::class, sprintf('%s@profileCreated', static::class));
    }
}
