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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('client_contact_people_id')->nullable();
            $table->integer('client_id');
            $table->integer('lead_id')->nullable();
            $table->dateTime('created_date');
            $table->dateTime('created_by');
            $table->integer('source_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
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
        Schema::dropIfExists('opportunities');
    }
};
