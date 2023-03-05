<?php

namespace Corals\Modules\Woo\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WooTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wc_fetch_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('integration_id')->nullable()->index();
            $table->string('mapper')->nullable();
            $table->string('status')->default('pending');

            $table->text('properties')->nullable();

            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
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
        Schema::dropIfExists('wc_fetch_requests');
    }
}
