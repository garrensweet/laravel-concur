<?php

namespace VdPoel\Concur\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use VdPoel\Concur\Test\Models\Event;
use VdPoel\Concur\Test\Models\Setting;

trait DatabaseSetup
{
    /**
     * @var string
     */
    protected $database;

    /**
     * @return void
     */
    protected function setUpDatabase(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->string('key');
            $table->longText('value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
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
     * @return void
     */
    protected function populateTestData(): void
    {
        $event = Event::create(['name' => 'Test Event']);

        $event->settings()->save(new Setting([
            'key' => 'concur_enabled',
            'value' => true
        ]));
    }
}
