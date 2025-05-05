<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('entrance');
            $table->dropColumn('pricing');
            $table->dropColumn('activities');
            $table->dropColumn('history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->string('entrance')->nullable();
            $table->string('pricing')->nullable();
            $table->string('activities')->nullable();
            $table->string('history')->nullable();
        });
    }
};
