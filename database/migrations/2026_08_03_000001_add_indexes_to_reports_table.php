<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * KENAPA MIGRATION INI DIBUAT:
     * Kolom user_id, hotel_id, shift_id di tabel `reports` didefinisikan sebagai
     * unsignedBigInteger() BIASA (bukan foreignId()->constrained()), artinya
     * kolom ini TIDAK otomatis punya index dari Laravel. Padahal kolom-kolom ini
     * adalah kolom yang paling sering dipakai untuk WHERE / whereHas / JOIN di
     * hampir semua controller (Admin Dashboard, Report History, Report Summary).
     *
     * Tanpa index, MySQL akan melakukan FULL TABLE SCAN setiap kali query
     * menyaring data laporan. Ini akan terasa sangat lambat begitu tabel
     * `reports` berisi ribuan baris (wajar untuk aplikasi laporan harian).
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Dipakai di: ReportController@history, DashboardController (staff)
            $table->index('user_id', 'reports_user_id_index');

            // Dipakai di: Admin\DashboardController, Admin\ReportSummaryController
            $table->index('hotel_id', 'reports_hotel_id_index');

            // Dipakai untuk menampilkan "Shift X" & filter per shift
            $table->index('shift_id', 'reports_shift_id_index');

            // Dipakai di HAMPIR SEMUA query: whereDate/whereBetween(report_date)
            // Composite index (report_date, status) karena keduanya sering
            // dipakai BERSAMAAN dalam satu WHERE clause di dashboard admin
            $table->index(['report_date', 'status'], 'reports_date_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_user_id_index');
            $table->dropIndex('reports_hotel_id_index');
            $table->dropIndex('reports_shift_id_index');
            $table->dropIndex('reports_date_status_index');
        });
    }
};
