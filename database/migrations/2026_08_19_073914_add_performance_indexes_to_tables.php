<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index('user_id', 'idx_reports_user_id');
            $table->index('hotel_id', 'idx_reports_hotel_id');
            $table->index('report_date', 'idx_reports_date');
            $table->index('is_late', 'idx_reports_is_late');
            $table->index('is_late_submit', 'idx_reports_is_late_submit');
        });

        Schema::table('report_items', function (Blueprint $table) {
            $table->index('report_id', 'idx_report_items_report_id');
            $table->index('status', 'idx_report_items_status');
            $table->index('is_additional', 'idx_report_items_is_additional');
        });

        Schema::table('point_histories', function (Blueprint $table) {
            $table->index('user_id', 'idx_point_histories_user_id');
            $table->index('created_at', 'idx_point_histories_created_at');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->index('is_read', 'idx_notes_is_read');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_user_id');
            $table->dropIndex('idx_reports_hotel_id');
            $table->dropIndex('idx_reports_date');
            $table->dropIndex('idx_reports_is_late');
            $table->dropIndex('idx_reports_is_late_submit');
        });

        Schema::table('report_items', function (Blueprint $table) {
            $table->dropIndex('idx_report_items_report_id');
            $table->dropIndex('idx_report_items_status');
            $table->dropIndex('idx_report_items_is_additional');
        });

        Schema::table('point_histories', function (Blueprint $table) {
            $table->dropIndex('idx_point_histories_user_id');
            $table->dropIndex('idx_point_histories_created_at');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex('idx_notes_is_read');
        });
    }
};
