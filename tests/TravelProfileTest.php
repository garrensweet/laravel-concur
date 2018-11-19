<?php

namespace VdPoel\Concur\Test;

use Illuminate\Support\Facades\Event;
use VdPoel\Concur\Events\TravelProfile\LookupTravelProfile;

class TravelProfileTest extends TestCase
{
    /** @test */
    public function it_listens_for_travel_profile_lookup_events()
    {
        $account = $this->createTestAccount();

        Event::fake();

        Event::assertDispatched(LookupTravelProfile::class, function (LookupTravelProfile $event) use ($account) {
            return $event->model->getKey() === $account->getKey();
        });
    }
}
