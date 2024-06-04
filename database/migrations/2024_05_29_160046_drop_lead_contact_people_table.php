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
        Schema::dropIfExists('lead_contact_people');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('lead_contact_people', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id');
            $table->integer('client_contact_people_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
