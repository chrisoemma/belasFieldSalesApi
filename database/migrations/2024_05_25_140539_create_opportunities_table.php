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
            $table->foreignId('company_id')->constrained('companies')->nullable();
            $table->integer('client_id')->constrained('clients')->nullable();
            $table->integer('lead_id')->constrained('leads')->nullable();
            $table->dateTime('close_date');
            $table->dateTime('created_date');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->integer('opportunity_forecast_id')->constrained('opportunity_forecasts')->nullable();
            $table->integer('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('opportunities');
    }
};
