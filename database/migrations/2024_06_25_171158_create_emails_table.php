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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->nullable();
            $table->text('subject')->nullable();
            $table->text('body')->nullable();
            $table->dateTime('sent_date')->nullable();
            $table->softDeletes();
            $table->foreignId('sender_id')->constrained('users')->nullable();
            $table->integer('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['Sent', 'Draft'])->default('Draft');
            $table->integer('recipient_id')->nullable();
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
        Schema::dropIfExists('emails');
    }
};
