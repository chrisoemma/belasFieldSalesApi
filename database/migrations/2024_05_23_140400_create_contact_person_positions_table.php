<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('contact_person_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('contact_person_id');
            $table->integer('position_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('contact_person_positions');
    }
};
