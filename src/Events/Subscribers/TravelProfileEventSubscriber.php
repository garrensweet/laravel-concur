<?php

namespace VdPoel\Concur\Events\Subscribers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Events\TravelProfile\CreateTravelProfile;
use VdPoel\Concur\Events\TravelProfile\LoginIdInUse;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;
use VdPoel\Concur\Events\TravelProfile\TravelProfileCreated;
use VdPoel\Concur\Events\TravelProfile\TravelProfileFound;
use VdPoel\Concur\Events\TravelProfile\TravelProfileNotFound;

class TravelProfileEventSubscriber extends BaseEventSubscriber
{
    /**
     * @var array
     */
    protected $events = [
        CreateTravelProfile::class ,
        LookupTravelProfile::class,
        TravelProfileFound::class,
        TravelProfileNotFound::class,
        TravelProfileCreated::class,
        LoginIdInUse::class
    ];

    /**
     * @param Authenticatable|Model $model
     */
    public function createTravelProfile($model): void
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
    public function lookupTravelProfile($model): void
    {
        $this->concur->travelProfile->get(['userid_value' => $model->getAttribute('email')]);
    }

    /**
     * @param Authenticatable|Model $model
     * @throws GuzzleException
     */
    public function travelProfileNotFound($model): void
    {
        $attributes        = $model->only(array_values(config('concur.form_params.travel.profile')));
        $params            = array_combine(array_keys(config('concur.form_params.travel.profile')), $attributes);
        $key               = app()->makeWith('concur.cache.key', compact('model'));
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
    public function travelProfileFound($payload): void
    {
        dump($payload);
    }

    /**
     * @param mixed $payload
     */
    public function travelProfileCreated($payload): void
    {
        dump($payload);
    }

    /**
     * @param mixed $payload
     */
    public function loginIdInUse($payload): void
    {
        dump($payload);
    }
}
