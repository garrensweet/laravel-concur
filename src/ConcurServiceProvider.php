<?php

namespace VdPoel\Concur;

use GoetasWebservices\XML\XSDReader\SchemaReader;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use VdPoel\Concur\Api\Authentication;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Api\TravelProfile;
use VdPoel\Concur\Api\User;
use VdPoel\Concur\Http\Middleware\Concur as ConcurMiddleware;

/**
 * Class ConcurServiceProvider
 * @package VdPoel\Concur
 */
class ConcurServiceProvider extends ServiceProvider
{
    /**
     * The middleware aliases.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'concur' => ConcurMiddleware::class
    ];

    /**
     * Bootstrap our package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([__DIR__ . '/../config/concur.php' => config_path('concur.php')], 'concur');

        Auth::extend('concur', function (Application $app, string $name, array $config) {
            return new ConcurGuard($app['concur.api.factory'], $app['auth']->createUserProvider($config['provider']), $app['request']);
        });
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
        $this->registerSchemaReader();
        $this->registerApiRequestHandlers();
        $this->aliasMiddleware();

        $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php');
    }

    /**
     * @return void
     */
    protected function registerAliases(): void
    {
        $this->app->alias('concur.auth.guard', ConcurGuard::class);
        $this->app->alias('concur.api.client', Client::class);
        $this->app->alias('concur.api.factory', Factory::class);
        $this->app->alias('concur.api.schema.reader', SchemaReader::class);
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
    protected function registerSchemaReader(): void
    {
        $this->app->bind(SchemaReader::class, function () {
            return new SchemaReader();
        });
    }

    /**
     * @return void
     */
    protected function registerApiRequestHandlers(): void
    {
        $this->app->singleton(Authentication::class, function (Application $app) {
            return new Authentication($app['concur.api.client'], $app['cache'], $app['concur.api.schema.reader']);
        });

        $this->app->singleton(TravelProfile::class, function (Application $app) {
            return new TravelProfile($app['concur.api.client'], $app['cache'], $app['concur.api.schema.reader']);
        });

        $this->app->singleton(User::class, function (Application $app) {
            return new User($app['concur.api.client'], $app['cache'], $app['concur.api.schema.reader']);
        });
    }

    /**
     * Alias the middleware.
     *
     * @return void
     */
    protected function aliasMiddleware()
    {
        $router = $this->app['router'];

        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->$method($alias, $middleware);
        }
    }
}
