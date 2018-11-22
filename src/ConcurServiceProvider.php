<?php

namespace VdPoel\Concur;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use VdPoel\Concur\Api\Authentication;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Api\TravelProfile;
use VdPoel\Concur\Api\User;
use VdPoel\Concur\Events\Subscribers\AuthenticationEventSubscriber;
use VdPoel\Concur\Events\Subscribers\TravelProfileEventSubscriber;

/**
 * Class ConcurServiceProvider
 * @package VdPoel\Concur
 */
class ConcurServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $packageConfig;

    /**
     * ConcurServiceProvider constructor.
     *
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->defer         = true;
        $this->packageConfig = __DIR__ . '/../config/concur.php';
    }

    /**
     * Bootstrap our package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([$this->packageConfig => config_path('concur.php')], 'concur');

        $this->mergeConfigFrom($this->packageConfig, 'concur');

        if (!class_exists('CreateConcurTravelProfilesTable')) {
            $this->publishes([
                __DIR__.'/../migrations/create_concur_travel_profiles_table.php.stub' => database_path("/migrations/{$timestamp}_create_activity_log_table.php"),
            ], 'migrations');
        }

        Event::subscribe(TravelProfileEventSubscriber::class);
        Event::subscribe(AuthenticationEventSubscriber::class);
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
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            Authentication::class,
            AuthenticationEventSubscriber::class,
            Factory::class,
            TravelProfile::class,
            TravelProfileEventSubscriber::class,
            User::class
        ];
    }

    /**
     * @return void
     */
    protected function registerAliases(): void
    {
        $this->app->alias('concur', Factory::class);
        $this->app->alias('concur.api.authentication', Authentication::class);
        $this->app->alias('concur.api.travel.profile', TravelProfile::class);
        $this->app->alias('concur.api.user', User::class);
    }

    /**
     * @return void
     */
    protected function registerConcurApiFactory(): void
    {
        $this->app->singleton('concur', function () {
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
