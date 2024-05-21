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
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->integer('check_in_id');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['Completed', 'Rescheduled', 'Canceled']);
            $table->string('input_location')->nullable();
            $table->decimal('near_latitude', 10, 7)->nullable();
            $table->decimal('near_longitude', 10, 7)->nullable();
            $table->string('comments')->nullable();
            $table->timestamp('checkout_time')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('check_outs');
    }
};
