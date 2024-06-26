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
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); 
            $table->enum('status', ['In Progress', 'Completed', 'Rescheduled', 'Canceled'])->default('In Progress');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('description')->nullable();
            $table->string('title')->nullable();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('set null');
            $table->foreignId('calendar_event_id')->nullable()->constrained('calendar_events')->onDelete('set null');
            $table->string('input_location')->nullable();
            $table->decimal('near_latitude', 10, 7)->nullable();
            $table->decimal('near_longitude', 10, 7)->nullable();
            $table->string('purpose');
            $table->foreignId('checkin_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamp('checkin_time')->nullable();
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
        Schema::dropIfExists('check_ins');
    }
};
