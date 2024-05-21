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
        Schema::create('company_contact_people', function (Blueprint $table) {
            $table->id();
            $table->string('fist_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('alt_phone_number')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->enum('gender',['male','female'])->nullable();
            $table->enum('status',['In Active','Active','Suspended','Deactivated','Banned','On Hold'])->nullable();
            $table->enum('prefered_channel',['email','phone','whatsapp','twitter'])->nullable();
            $table->integer('position_id')->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('company_contact_people');
    }
};
