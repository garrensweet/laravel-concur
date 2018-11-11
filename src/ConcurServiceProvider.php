<?php

namespace VdPoel\Concur;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use VdPoel\Concur\Api\Authentication;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Api\TravelProfile;
use VdPoel\Concur\Api\User;

/**
 * Class ConcurServiceProvider
 * @package VdPoel\Concur
 */
class ConcurServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap our package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([__DIR__ . '/../config/concur.php' => config_path('concur.php')], 'concur');

//        Auth::extend('concur', function (Application $app, string $name, array $config) {
//            return new ConcurGuard($app['concur.api.factory'], $app['auth']->createUserProvider($config['provider']), $app['request']);
//        });
    }

    /**
     * Register our package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerAliases();
        $this->registerConcurApiFactory();
        $this->registerGuzzleClient();
        $this->registerApiRequestHandlers();

        $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php');
    }

    /**
     * @return void
     */
    protected function registerAliases(): void
    {
        $this->app->alias('concur.auth.guard', ConcurGuard::class);
        $this->app->alias('concur.api.factory', Factory::class);
        $this->app->alias('concur.api.authentication', Authentication::class);
        $this->app->alias('concur.api.travel.profile', TravelProfile::class);
        $this->app->alias('concur.api.user', User::class);
    }

    /**
     * @return void
     */
    protected function registerConcurApiFactory(): void
    {
        $this->app->singleton('concur.api.factory', function () {
            return new Factory();
        });
    }

    /**
     * @return void
     */
    protected function registerGuzzleClient(): void
    {
        $this->app->bind(Client::class, function () {
            return new Client();
        });
    }

    /**
     * @return void
     */
    protected function registerApiRequestHandlers(): void
    {
        $this->app->singleton('concur.api.authentication', function (Application $app) {
            return new Authentication($app->make(Client::class), $app->make('cache'));
        });

        $this->app->singleton('concur.api.travel.profile', function (Application $app) {
            return new TravelProfile($app->make(Client::class), $app->make('cache'));
        });

        $this->app->singleton('concur.api.user', function (Application $app) {
            return new User($app->make(Client::class), $app->make('cache'));
        });
    }
}
