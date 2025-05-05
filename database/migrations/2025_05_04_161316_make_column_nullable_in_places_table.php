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
            $table->string('pricing')->nullable()->change();
            $table->string('history')->nullable()->change();
            $table->string('activities')->nullable()->change();
            $table->string('entrance')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->string('pricing')->nullable(false)->change();
            $table->string('history')->nullable(false)->change();
            $table->string('activities')->nullable(false)->change();
            $table->string('entrance')->nullable(false)->change();
        });
    }
};
