<?php

namespace VdPoel\Concur\Events\Subscribers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Events\Dispatcher;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Events\TravelProfile\CreateTravelProfile;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;
use VdPoel\Concur\Test\Models\Account;

class TravelProfileEventSubscriber
{
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
     * @param CreateTravelProfile $event
     */
    public function create (CreateTravelProfile $event)
    {
        try {
            $attributes = $event->model->only(array_values(config('concur.form_params.travel.profile')));

            $params = array_combine(array_keys(config('concur.form_params.travel.profile')), $attributes);

            $this->concur->travelProfile->create($params);
        } catch (ClientException $exception) {
            logger($exception->getMessage());
        } catch (GuzzleException $exception) {
            logger($exception->getMessage());
        }
    }

    /**
     * @param Account $account
     */
    public function lookup (Account $account)
    {
        try {
            $this->concur->travelProfile->get(['userid_value' => $account->getAttribute('email')]);
        } catch (ClientException $exception) {
            dump($account->getAttribute('email'));
            dd($exception->getMessage());
        } catch (GuzzleException $exception) {
            dump($account->getAttribute('email'));
            dd($exception->getMessage());
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(CreateTravelProfile::class, 'VdPoel\Concur\Events\Subscribers\TravelProfileEventSubscriber@create');
        $events->listen(LookupTravelProfile::class, 'VdPoel\Concur\Events\Subscribers\TravelProfileEventSubscriber@lookup');
    }
}
