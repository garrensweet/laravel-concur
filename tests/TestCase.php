<?php

namespace VdPoel\Concur\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\ConcurServiceProvider;
use VdPoel\Concur\Test\Models\Account;
use VdPoel\Concur\Test\Models\Event;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    /**
     * @var Factory
     */
    protected $concur;

    /**
     * @var string
     */
    protected $database;

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
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        Event::create(['name' => 'Test Event']);

        $this->app->instance('request', Request::create('/', 'POST', [
            'event_id'   => Event::all()->first()->getKey(),
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => sprintf('test.user+%s@example.com', md5(Str::uuid())),
            'password'   => md5(Str::uuid())
        ]));

        $this->concur = $this->app->make(Factory::class);
//        $this->readCredentialsFile();
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ConcurServiceProvider::class];
    }

    /**
     * @param Application $app
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        $app['auth']->guard()->getProvider()->setModel(Account::class);

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
    public function markTestAsPassed()
    {
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    protected function readCredentialsFile(): void
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . 'credentials.json';

        if (file_exists($filename)) {
            $credentials = json_decode(file_get_contents($filename), true);

            if (is_array($credentials) && json_last_error() === JSON_ERROR_NONE) {
                $this->concur->authentication->setCacheData($credentials);
            }
        }
    }

    /**
     * @return void
     */
    protected function setUpDatabase()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('travel_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->longText('xml');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
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
