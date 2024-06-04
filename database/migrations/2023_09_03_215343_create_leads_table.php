<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()

    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('client_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->dateTime('created_date');
            $table->string('title')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('position_id')->nullable();
            $table->integer('created_by');
            $table->integer('source_id')->nullable();
            $table->boolean('is_converted')->default('false');
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
        Schema::dropIfExists('leads');
    }
};
