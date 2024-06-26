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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->nullable();
            $table->dateTime('call_date')->nullable();
            $table->string('duration')->nullable();
            $table->text('agenda')->nullable();
            $table->softDeletes();
            $table->integer('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Cancelled'])->default('Completed');
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
        Schema::dropIfExists('call_logs');
    }
};
