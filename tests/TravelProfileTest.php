<?php

namespace VdPoel\Concur\Test;

class TravelProfileTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->concur->authentication->login();
    }

    /** @test */
    public function it_finds_the_encrypted_password_in_the_cache()
    {
        $this->withoutEvents();

        $model = $this->createTestAccount();

        $key = app()->makeWith('concur.cache.key', compact('model'));

        $encryptedPassword = $this->app['cache']->get($key);

        $this->assertNotEmpty($encryptedPassword);

        $this->assertTrue($this->app['hash']->check(decrypt($encryptedPassword), $model->getAttribute('password')));
    }

    /** @test */
    public function it_creates_travel_profiles_for_new_accounts()
    {
        $model = $this->createTestAccount();
    }
}
