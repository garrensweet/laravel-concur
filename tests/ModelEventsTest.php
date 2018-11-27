<?php

namespace VdPoel\Concur\Test;

use Illuminate\Support\Facades\Event;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;
use VdPoel\Concur\Events\TravelProfile\TravelProfileCreated;
use VdPoel\Concur\Events\TravelProfile\TravelProfileNotFound;
use VdPoel\Concur\Observers\AuthenticatableObserver;

class ModelEventsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->getAuthenticatableModel()::flushEventListeners();
        $this->getAuthenticatableModel()::boot();

        app($this->getAuthenticatableModel())::observe(AuthenticatableObserver::class);

        $this->concur->authentication->login();
    }

    /** @test */
    public function it_caches_account_attributes()
    {
        $this->withoutEvents();

        $account = $this->createTestAccount();

        $key = md5(implode('.', $account->only(['first_name', 'last_name', 'email', 'event_id'])));

        $data = decrypt($this->app['cache']->get($key));

        $this->assertTrue($this->app->make('hash')->check($data, $account->getAttribute('password')));
    }

    /** @test */
    public function it_fires_lookup_travel_profile_event_after_model_is_created()
    {
        Event::fake(LookupTravelProfile::class);

        $account = $this->createTestAccount();

        Event::assertDispatched(LookupTravelProfile::class, function () use ($account) {
            return data_get(func_get_arg(1), 'id') === $account->getKey();
        });
    }

    /** @test */
    public function it_fires_travel_profile_not_found_event_for_new_accounts()
    {
        Event::fake(TravelProfileNotFound::class);

        $account = $this->createTestAccount();

        Event::assertDispatched(TravelProfileNotFound::class, function () use ($account) {
            return data_get(func_get_arg(1), 'id') === $account->getKey();
        });
    }

    /** @test */
    public function it_fires_travel_profile_created_event()
    {
        Event::fake(TravelProfileCreated::class);

        $account = $this->createTestAccount();

        Event::assertDispatched(TravelProfileCreated::class, function () use ($account) {
            return data_get(func_get_arg(1), 'id') === $account->getKey();
        });
    }
}
