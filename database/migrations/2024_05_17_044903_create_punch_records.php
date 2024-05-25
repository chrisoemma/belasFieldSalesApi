<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('punch_records', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('punch_in_location_id')->nullable();
            $table->integer('punch_out_location_id')->nullable();
            $table->dateTime('punch_in_time');
            $table->dateTime('punch_out_time')->nullable();
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
        Schema::dropIfExists('punch_records');
    }
};
