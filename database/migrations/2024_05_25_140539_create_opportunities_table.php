<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up()

    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('company_id');
            $table->integer('client_id')->nullable();
            $table->integer('lead_id')->nullable();
            $table->dateTime('close_date');
            $table->dateTime('created_date');
            $table->integer('created_by');
            $table->integer('source_id')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->integer('opportunity_forecast_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('opportunities');
    }
};
