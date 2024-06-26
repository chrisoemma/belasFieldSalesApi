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
        Schema::create('calender_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->nullable();
            $table->text('subject')->nullable();
            $table->text('agenda')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('duration')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('user_id')->nullable();
            $table->foreignId('client_id')->constrained('clients')->nullable();
            $table->softDeletes();
            $table->integer('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Cancelled'])->default('Scheduled');
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
        Schema::dropIfExists('calender_events');
    }
};
