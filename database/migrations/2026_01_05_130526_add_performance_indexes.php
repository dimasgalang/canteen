<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('canteen', function (Blueprint $table) {
            // Composite index for common search patterns (DataTables)
            $table->index(['date', 'npk', 'canteen_no'], 'idx_canteen_dt');
            // Composite index for Scanner (Checking duplicate scans)
            $table->index(['npk', 'created_at'], 'idx_canteen_scanner');
        });

        Schema::table('canteen_twos', function (Blueprint $table) {
            // Composite index for common search patterns (DataTables)
            $table->index(['date', 'npk', 'canteen_no'], 'idx_canteen_twos_dt');
            // Composite index for Scanner (Checking duplicate scans)
            $table->index(['npk', 'created_at'], 'idx_canteen_twos_scanner');
        });
    }

    public function down()
    {
        Schema::table('canteen', function (Blueprint $table) {
            $table->dropIndex('idx_canteen_performance');
            $table->dropIndex(['created_at']);
        });

        Schema::table('canteen_twos', function (Blueprint $table) {
            $table->dropIndex('idx_canteen_twos_performance');
            $table->dropIndex(['created_at']);
        });
    }
};
