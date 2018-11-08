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
        $this->app->when(Client::class)
            ->needs('$connection')
            ->give(new \GuzzleHttp\Client([
                'base_uri' => config('concur.api_url_prefix')
            ]));

    }

    /**
     * Expose services provided by our package.
     *
     * @return array
     */
    public function provides(): array
    {
        return [Client::class];
    }
}