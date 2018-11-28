<?php

namespace VdPoel\Concur;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use VdPoel\Concur\Api\Authentication;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Api\TravelProfile;
use VdPoel\Concur\Api\User;
use VdPoel\Concur\Events\Subscribers\AuthenticationEventSubscriber;
use VdPoel\Concur\Events\Subscribers\TravelProfileEventSubscriber;
use VdPoel\Concur\Events\Subscribers\UserEventSubscriber;
use VdPoel\Concur\Observers\AuthenticatableObserver;

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

        $this->defer         = false;
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

        $this->loadRoutesFrom(__DIR__ . '/../routes/concur.php');

        $this->app->make($this->app['concur.auth.model'])::observe(AuthenticatableObserver::class);

        Event::subscribe(AuthenticationEventSubscriber::class);
//        Event::subscribe(TravelProfileEventSubscriber::class);
//        Event::subscribe(UserEventSubscriber::class);
    }

    /**
     * Register our package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerAliases();
        $this->registerStaticBindings();
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
            Concur::class,
            Factory::class,
            TravelProfile::class,
            TravelProfileEventSubscriber::class,
            User::class,
            UserEventSubscriber::class
        ];
    }

    /**
     * @return void
     */
    protected function createMigrationFiles(): void
    {
        config(['filesystems.disks' => array_merge(config('filesystems.disks'), [
            'concur' => [
                'driver' => 'local',
                'root'   => __DIR__ . '/../database/migrations',
            ]
        ])]);

        $exists = in_array('create_travel_profiles_table.php', array_map(function (string $filename) {
            return implode('_', array_slice(explode('_', $filename), 4));
        }, Storage::disk('concur')->files()));

        if (!$exists) {
            Storage::disk('concur')->copy('stubs/create_travel_profiles_table.php', sprintf('%s_create_travel_profiles_table.php', now()->format('Y_m_d_Hms')));
        }
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
    protected function registerStaticBindings(): void
    {
        /**
         * Register a function to get the guard's user model.
         */
        $this->app->bind('concur.auth.model', function (Application $app) {
            return $app['auth']->guard('api-v3')->getProvider()->getModel();
        });

        /**
         * Register a function a create a cache key prefix unique to each user.
         */
        $this->app->bind('concur.cache.key', function (Application $application, array $parameters) {
            $attributes = ['first_name', 'last_name', 'email'];

            if ($application['config']->get('concur.migrations.tenancy.enabled', false)) {
                array_push($attributes, $application['config']->get('concur.migrations.tenancy.foreign_key'));
            }

            return md5(implode('.', data_get($parameters, 'model')->only($attributes)));
        });
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
