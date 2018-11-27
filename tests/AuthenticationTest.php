<?php

namespace VdPoel\Concur\Test;

use Illuminate\Database\Eloquent\Model;
use VdPoel\Concur\Test\Models\Account;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function it_receives_the_user_providers_model_from_the_guard()
    {
        $class = $this->app['auth']->guard()->getProvider()->getModel();

        $this->assertEquals(Account::class, $class);

        $this->assertInstanceOf(Model::class, app($class));
    }

    /** @test */
    public function it_authenticates_with_concur_web_services()
    {
        $response = $this->concur->authentication->login();

        $this->assertTrue($response);
    }

    /** @test */
    public function it_caches_the_authentication_response_fields()
    {
        $this->concur->authentication->login();

        $fields = [
            "expires_in",
            "scope",
            "token_type",
            "access_token",
            "refresh_token",
            "refresh_expires_in",
            "id_token",
            "geolocation"
        ];

        foreach ($fields as $field) {
            $data = $this->concur->authentication->getCachedData($field);

            $this->assertNotEmpty($data);
        }
    }
}
