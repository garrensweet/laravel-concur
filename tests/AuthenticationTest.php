<?php

namespace VdPoel\Concur\Test;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function it_authenticates_with_concur_web_services()
    {
        $response = $this->concur->authentication->login();

        $this->assertTrue($response);
    }

    /** @test */
    public function it_caches_the_authentication_response_fields()
    {

    }
}
