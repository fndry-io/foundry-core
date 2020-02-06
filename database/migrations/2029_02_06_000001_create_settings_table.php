<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {

            $table->increments('id');
            $table->string('domain', 100)->comment('Plugin domain');
            $table->string('name', 100)->comment('Setting name');
	        $table->string('type', 10)->comment('Type of value: bool, int, integer, boolean, string, double, array');
            $table->string('default')->comment('Setting default value')->nullable();
            $table->string('value')->comment('Changed Setting value')->nullable();
            $table->string('model')->comment('Model for this setting');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
