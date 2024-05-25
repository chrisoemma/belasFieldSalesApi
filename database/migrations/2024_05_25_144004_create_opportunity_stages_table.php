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
            $table->string('name');
            $table->integer('opportunity_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('client_id');
            $table->string('stage');
            $table->dateTime('created_date');
            $table->integer('created_by');
            $table->integer('client_contact_people_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('opportunity_stages');
    }
};
