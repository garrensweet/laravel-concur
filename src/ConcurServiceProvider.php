<?php

namespace VdPoel\Concur;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ConcurServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap our package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/concur.php' => config_path('concur.php'),
        ], 'concur-config');
    }

    /**
     * Register our package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ConcurCredentials::class, function (Application $app) {
            return new ConcurCredentials([
                'client_id' => config('concur.client_id'),
                'client_secret' => config('concur.client_secret'),
                'grant_type' => config('concur.grant_type'),
                'username' => config('concur.username'),
                'password' => config('concur.password')
            ]);
        });

        $this->app->singleton('ConcurGuzzleClient', function (Application $app) {
            return new \GuzzleHttp\Client([
                'base_uri' => config('concur.api_url_prefix'),
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ]
            ]);
        });

        $this->app->when(ConcurClient::class)
            ->needs('$connection')
            ->give($this->app['ConcurGuzzleClient']);

        $this->app->when(ConcurClient::class)
            ->needs('$credentials')
            ->give($this->app[ConcurCredentials::class]);

    }

    /**
     * Expose services provided by our package.
     *
     * @return array
     */
    public function provides(): array
    {
        return [ConcurClient::class];
    }
}