<?php

namespace VdPoel\Concur;

use Illuminate\Support\ServiceProvider;

class ConcurServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap our package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([__DIR__ . '/../config/concur.php' => config_path('concur.php')], 'concur');

        $this->loadRoutesFrom(__DIR__ . '/../routes/concur.php');
    }

    /**
     * Register our package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Concur::class, function () {
            return new Concur($this->app);
        });
    }

    /**
     * Expose services provided by our package.
     *
     * @return array
     */
    public function provides(): array
    {
        return [Concur::class];
    }
}
