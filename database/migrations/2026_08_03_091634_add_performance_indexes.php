<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('hotel_id');
            $table->index('shift_id');
            $table->index('report_date');
            $table->index('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('hotel_id');
            $table->index('department');
            $table->index('role');
        });

        Schema::table('report_items', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['hotel_id']);
            $table->dropIndex(['shift_id']);
            $table->dropIndex(['report_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['hotel_id']);
            $table->dropIndex(['department']);
            $table->dropIndex(['role']);
        });

        Schema::table('report_items', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
