<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('site_pages');

        Schema::create('foundry_builder_pages', function (Blueprint $table) {

            $table->increments('id');
            $table->string('short_id', 50)->nullable();
            $table->string('name');
            $table->string('url');
            $table->string('resource_type')->nullable();
            $table->unsignedInteger('resource_id')->nullable();
            $table->unsignedBigInteger('layout_id')->nullable();
            $table->unsignedBigInteger('content_layout_id')->nullable();
            $table->string('status',20)->default('draft');
            $table->json('seo')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('foundry_builder_pages', 'site_pages');
    }
}
