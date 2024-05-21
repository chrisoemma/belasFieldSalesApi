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
        Schema::create('check_ins', function (Blueprint $table) {

            $table->id();
            $table->integer('user_id');
            $table->integer('client_id')->nullable(); 
            $table->integer('meeting_id')->nullable();
            $table->enum('status', ['In Progress', 'Completed', 'Rescheduled', 'Canceled'])->default('In Progress');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('description')->nullable();
            $table->string('title')->nullable();
            $table->integer('task_id')->nullable(); 
            $table->string('input_location')->nullable();
            $table->decimal('near_latitude', 10, 7)->nullable();
            $table->decimal('near_longitude', 10, 7)->nullable();
            $table->string('purpose');
            $table->integer('checkin_by')->nullable();
            $table->softDeletes();
            $table->timestamp('checkin_time')->nullable();
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
        Schema::dropIfExists('check_ins');
    }
};
