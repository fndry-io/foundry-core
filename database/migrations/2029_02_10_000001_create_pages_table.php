<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_pages', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->string('uuid', 50);
            $table->string('path');
            $table->json('meta')->nullable();
            $table->string('layout',100);
            $table->string('width', 20)->nullable();
            $table->string('height',20)->nullable();
            $table->json('styles')->nullable();
            $table->string('classes')->nullable();
            $table->json('children')->nullable();

            $table->string('resource_type')->nullable();
            $table->unsignedInteger('resource_id')->nullable();

              $table->unsignedInteger('site_id');

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
        Schema::dropIfExists('site_pages');
    }
}
