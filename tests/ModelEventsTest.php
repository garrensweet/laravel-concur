<?php

namespace VdPoel\Concur\Test;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Event;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;
use VdPoel\Concur\Events\TravelProfile\TravelProfileCreated;
use VdPoel\Concur\Events\TravelProfile\TravelProfileNotFound;
use VdPoel\Concur\Observers\AuthenticatableObserver;

class ModelEventsTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->getAuthenticatableModel()::flushEventListeners();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getAuthenticatableModel()::boot();

        app($this->getAuthenticatableModel())::observe(AuthenticatableObserver::class);

        $this->concur->authentication->login();
    }

    /** @test */
    public function it_caches_account_attributes(): void
    {
        $this->withoutEvents();

        $account = $this->createTestAccount();

        $key = md5(implode('.', $account->only(['first_name', 'last_name', 'email', 'event_id'])));

        $data = decrypt($this->app['cache']->get($key));

        $this->assertTrue($this->app->make('hash')->check($data, $account->getAttribute('password')));
    }

    /** @test */
    public function it_fires_lookup_travel_profile_event_after_model_is_created(): void
    {
        Event::fake(LookupTravelProfile::class);

        $account = $this->createTestAccount();

        /** @noinspection PhpUndefinedMethodInspection */
        Event::assertDispatched(LookupTravelProfile::class, function () use ($account) {
            return data_get(func_get_arg(1), 'id') === $account->getKey();
        });
    }

    /** @test */
    public function it_fires_travel_profile_not_found_event_for_new_accounts(): void
    {
        Event::fake(TravelProfileNotFound::class);

        $account = $this->createTestAccount();

        /** @noinspection PhpUndefinedMethodInspection */
        Event::assertDispatched(TravelProfileNotFound::class, function () use ($account) {
            return data_get(func_get_arg(1), 'id') === $account->getKey();
        });
    }

    /** @test */
    public function it_fires_travel_profile_created_event(): void
    {
        Event::fake(TravelProfileCreated::class);

        $account = $this->createTestAccount();

        /** @noinspection PhpUndefinedMethodInspection */
        Event::assertDispatched(TravelProfileCreated::class, function () use ($account) {
            return data_get(func_get_arg(1), 'id') === $account->getKey();
        });
    }
}
