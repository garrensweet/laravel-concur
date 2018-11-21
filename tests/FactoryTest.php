<?php

namespace VdPoel\Concur\Test;

use Illuminate\Contracts\Cache\Store;
use VdPoel\Concur\Api\Authentication;
use VdPoel\Concur\Api\TravelProfile;

class FactoryTest extends TestCase
{
    /**
     * @var Store
     */
    protected $cache;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set(['cache.default' => 'redis']);

        $this->cache = $this->app->make('cache');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->app['config']->set(['cache.default' => 'array']);

        unset($this->cache);

        parent::tearDown();
    }

    /** @test */
    public function it_sets_the_cache_prefix_for_authentication()
    {
        $instance = $this->concur->authentication;

        $this->assertInstanceOf(Authentication::class, $instance);

        $this->assertSame('Concur.Authentication:', $this->cache->getPrefix());
    }

    /** @test */
    public function it_sets_the_cache_prefix_for_travel_profile()
    {
        $instance = $this->concur->travelProfile;

        $this->assertInstanceOf(TravelProfile::class, $instance);

        $this->assertSame('Concur.TravelProfile:', $this->cache->getPrefix());
    }
}
