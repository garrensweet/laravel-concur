<?php

namespace VdPoel\Concur\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use VdPoel\Concur\Api\Factory;
use VdPoel\Concur\ConcurServiceProvider;
use VdPoel\Concur\Test\Models\Account;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    /**
     * @var Factory
     */
    protected $concur;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->concur = $this->app->make(Factory::class);
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
        $app['config']->set('auth.providers.users.model', Account::class);

        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => __DIR__ . DIRECTORY_SEPARATOR . 'database.sqlite'
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
    protected function setUpDatabase()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * @return Account
     */
    protected function createTestAccount(): Account
    {
        return Account::create([
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => sprintf('test.user+%s@example.com', md5(Str::uuid()))
        ]);
    }
}
