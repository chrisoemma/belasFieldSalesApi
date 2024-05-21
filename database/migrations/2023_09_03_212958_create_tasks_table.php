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
            $table->string('title');
            $table->string('decription')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->enum('status',['Pending','In Progress','Completed']);
            $table->integer('assigned_to');
            $table->integer('assigned_by');
            $table->enum('priority',['Low','Medium','High','Urgent']);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('company_id');
            $table->integer('lead_id')->nullable();
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
