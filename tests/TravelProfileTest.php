<?php

namespace VdPoel\Concur\Test;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;

class TravelProfileTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->concur->authentication->login();
    }

    /** @test */
    public function it_finds_the_encrypted_password_in_the_cache(): void
    {
        $this->withoutEvents();

        $model             = $this->createTestAccount();
        $key               = app()->makeWith('concur.cache.key', compact('model'));
        $encryptedPassword = $this->app['cache']->get($key);

        $this->assertNotEmpty($encryptedPassword);
        $this->assertTrue($this->app['hash']->check(decrypt($encryptedPassword), $model->getAttribute('password')));
    }

    /** @test */
    public function it_creates_travel_profiles_for_new_accounts(): void
    {
        $model = $this->createTestAccount();

        $this->concur->travelProfile->get(['userid_value' => $model->getAttribute('email')]);
    }
}
