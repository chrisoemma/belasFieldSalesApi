<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    
    {
        Schema::create('opportunity_stages', function (Blueprint $table) {
            $table->id();
            $table->integer('opportunity_id')->nullable();
            $table->string('stage');
            $table->dateTime('created_date');
            $table->integer('created_by');
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('probability', 5, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('opportunity_stages');
    }
};
