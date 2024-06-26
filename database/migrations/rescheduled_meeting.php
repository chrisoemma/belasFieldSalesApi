<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('rescheduled_meetings', function (Blueprint $table) {
            $table->id();
            $table->integer('check_in_id')->nullable();
            $table->integer('meeting_id')->nullable();
            $table->dateTime('from');
            $table->dateTime('to');
            $table->string('rescheduled_meeting_location');
            $table->text('reason')->nullable();
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
        Schema::dropIfExists('rescheduled_meetings');
    }
};
