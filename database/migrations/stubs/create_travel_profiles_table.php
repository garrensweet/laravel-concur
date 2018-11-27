<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTravelProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('travel_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            if (config('concur.migrations.tenancy.enabled')) {
                $table->unsignedBigInteger(config('concur.migrations.tenancy.foreign_key'));
            }
            $table->unsignedBigInteger('profilable_id');
            $table->string('profilable_type');
            $table->json('content');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_profiles');
    }
}
