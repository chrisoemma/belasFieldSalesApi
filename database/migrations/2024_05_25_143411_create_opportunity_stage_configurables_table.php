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
        Schema::create('opportunity_stage_configurables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->nullable();
            $table->string('flag')->nullable();
            $table->integer('company_id');
            $table->decimal('probability', 5, 2)->nullable();
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
        Schema::dropIfExists('opportunity_stage_configurables');
    }
};
