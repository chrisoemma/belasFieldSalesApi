<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('lead_status_defaults', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->nullable();
            $table->string('flag')->nullable();//continue,lost, convert
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_status_defaults');
    }
};
