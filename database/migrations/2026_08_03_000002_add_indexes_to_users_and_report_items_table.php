<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CATATAN PENTING (koreksi dari audit sebelumnya):
     * Kolom `users.hotel_id` dan `users.shift_id` SUDAH otomatis punya index
     * karena didefinisikan pakai foreignId()->constrained() di migration awal
     * (Laravel otomatis membuat index saat foreign key constraint dibuat).
     * Jadi kita TIDAK perlu index ulang untuk kedua kolom itu.
     *
     * Yang benar-benar belum ada index-nya dan sering dipakai untuk filter:
     * - users.department  (dipakai di hampir semua controller Admin untuk filter staf)
     * - users.role        (dipakai untuk memisahkan staff vs admin)
     * - report_items.status (dipakai untuk hitung completed/pending di summary & export)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('department', 'users_department_index');
            $table->index('role', 'users_role_index');
        });

        Schema::table('report_items', function (Blueprint $table) {
            $table->index('status', 'report_items_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_department_index');
            $table->dropIndex('users_role_index');
        });

        Schema::table('report_items', function (Blueprint $table) {
            $table->dropIndex('report_items_status_index');
        });
    }
};
