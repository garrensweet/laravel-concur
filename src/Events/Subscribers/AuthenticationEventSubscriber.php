<?php

namespace VdPoel\Concur\Events\Subscribers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Events\Dispatcher;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Events\TravelProfile\CreateTravelProfile;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;
use VdPoel\Concur\Test\Models\Account;

class AuthenticationEventSubscriber
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
     * @param CreateTravelProfile $event
     * @return void
     */
    public function login (CreateTravelProfile $event): void
    {

    }

    /**
     * @param Account $account
     * @return void
     */
    public function refresh (Account $account): void
    {

    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     * @return void
     */
    public function subscribe($events): void
    {
        $events->listen(CreateTravelProfile::class, 'VdPoel\Concur\Events\Subscribers\TravelProfileEventSubscriber@create');
        $events->listen(LookupTravelProfile::class, 'VdPoel\Concur\Events\Subscribers\TravelProfileEventSubscriber@lookup');
    }
}
