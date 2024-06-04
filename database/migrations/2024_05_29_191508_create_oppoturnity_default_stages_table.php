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
        Schema::create('oppoturnity_default_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->nullable();
            $table->string('flag')->nullable();
            $table->decimal('probability', 5, 2)->nullable();
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
        Schema::dropIfExists('oppoturnity_default_stages');
    }
};
