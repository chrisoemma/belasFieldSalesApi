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
        Schema::create('account_calender_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('calender_event_id')->constrained('calender_events')->onDelete('cascade');
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
        Schema::dropIfExists('account_calender_events');
    }
};
