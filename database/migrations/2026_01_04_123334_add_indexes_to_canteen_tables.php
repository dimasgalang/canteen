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
        Schema::table('canteen', function (Blueprint $table) {
            $table->index(['npk', 'created_at']);
        });

        Schema::table('canteen_twos', function (Blueprint $table) {
            $table->index(['npk', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canteen', function (Blueprint $table) {
            $table->dropIndex(['npk', 'created_at']);
        });

        Schema::table('canteen_twos', function (Blueprint $table) {
            $table->dropIndex(['npk', 'created_at']);
        });
    }
};
