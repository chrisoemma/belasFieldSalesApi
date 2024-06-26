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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->nullable();
            $table->string('subject');
            $table->text('decription')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->enum('status',['Not Started','In Progress','Completed','Deferred','Waiting on someone else']);
            $table->integer('assigned_to')->constrained('users')->nullable();
            $table->integer('assigned_by')->constrained('users')->nullable();
            $table->enum('priority',['Low','Medium','High','Urgent']);
            $table->foreignId('checkin_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('tasks');
    }
};
