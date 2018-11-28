<?php

namespace VdPoel\Concur\Test;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\Concur;
use VdPoel\Concur\ConcurServiceProvider;
use VdPoel\Concur\Test\Models\Account;
use VdPoel\Concur\Test\Models\Event;

abstract class TestCase extends OrchestraTestCase
{
    use DatabaseSetup, RefreshDatabase;

    /**
     * @var Factory
     */
    protected $concur;

    /**
     * TestCase constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->database = __DIR__ . DIRECTORY_SEPARATOR . 'database.sqlite';

        if (file_exists($this->database)) {
            unlink($this->database);
        }
    }

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->populateTestData();
        $this->bindRequestInstance();

        $this->concur = $this->app->make(Factory::class);

        Concur::auth(function (Model $model) {
            return filter_var($model->loadMissing('event.settings')->event->settings()->where('key', config('concur.auth.setting.key'))->first()->getAttribute('value'), FILTER_VALIDATE_BOOLEAN);
        });
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [ConcurServiceProvider::class];
    }

    /**
     * @param Application $app
     * @return void
     */
    public function getEnvironmentSetUp($app): void
    {
        $app['auth']->guard()->getProvider()->setModel(Account::class);

        $app['config']->set('database.redis.default', [
            'host'     => env('REDIS_SERVER', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
        ]);

        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => $this->database
        ]);

        $app['config']->set('database.default', 'testing');

        $contents = file_get_contents(__DIR__ . '/../.env.testing');

        foreach (array_filter(explode("\n", $contents)) as $line) {
            putenv($line);
        }

        $app['config']->set('concur', require __DIR__ . '/../config/concur.php');
    }

    /**
     * @return void
     */
    public function markTestAsPassed(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    protected function bindRequestInstance(): void
    {
        $this->app->instance('request', Request::create('/', 'POST', [
            'event_id'   => Event::all()->first()->getKey(),
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => sprintf('test.%s@example.com', md5(Str::uuid())),
            'password'   => md5(Str::uuid())
        ]));
    }

    /**
     * @return Account
     */
    protected function createTestAccount(): Account
    {
        $attributes = array_merge(
            request()->only(['first_name', 'last_name', 'email']),
            [
                'password' => $this->app->make('hash')->make(request()->input('password')),
                'event_id' => Event::all()->first()->getKey()
            ]
        );

        return app($this->getAuthenticatableModel())->create($attributes);
    }

    /**
     * @return string
     */
    protected function getAuthenticatableModel(): string
    {
        return $this->app['concur.auth.model'];
    }
}
