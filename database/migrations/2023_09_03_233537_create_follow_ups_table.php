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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id');
            $table->dateTime('follow_up_date');
            $table->enum('action',['Call', 'Email', 'Meeting']);
            $table->enum('outcome', ['Scheduled Call','Scheduled Meeting', 'Sent Proposal']); 
            $table->text('description')->nullable();
            $table->enum('status', ['Scheduled', 'Pending','Completed']); 
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
        Schema::dropIfExists('follow_ups');
    }
};
