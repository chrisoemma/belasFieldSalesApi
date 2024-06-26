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
        Schema::create('contact_call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('client_contact_people')->onDelete('cascade');
            $table->foreignId('call_log_id')->constrained('call_logs')->onDelete('cascade');
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
        Schema::dropIfExists('contact_call_logs');
    }
};
