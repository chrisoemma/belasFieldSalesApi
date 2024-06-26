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
        Schema::create('leads', function (Blueprint $table) {
            
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->nullable();
            $table->string('client_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->dateTime('created_date');
            $table->string('title')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->integer('position_id')->nullable()->constrained('positions')->onDelete('set null');
            $table->integer('industry_id')->nullable()->constrained('industries')->onDelete('set null');
            $table->integer('source_id')->nullable()->constrained('sources')->onDelete('set null');
            $table->boolean('is_converted')->default(false);
            $table->string('website')->nullable();
            $table->string('number_of_employees')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('leads');
    }
};
