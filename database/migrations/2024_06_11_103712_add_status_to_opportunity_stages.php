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
        Schema::table('opportunity_stages', function (Blueprint $table) {
            $table->enum('status', ['Open', 'Won', 'Lost'])->default('Open')->after('stage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opportunity_stages', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
