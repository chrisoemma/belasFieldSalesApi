<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('check_in_assets', function (Blueprint $table) {
            $table->id();
            $table->integer('check_in_id');
            $table->string('url')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('check_in_assets');
    }
};
