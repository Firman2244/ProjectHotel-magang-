<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_items', function (Blueprint $table) {
            // Kategori beban kerja (ringan/sedang/berat), diisi MANUAL oleh admin
            // pada saat proses ACC (verifikasi) tugas. Tidak otomatis dari sistem.
            $table->enum('difficulty', ['ringan', 'sedang', 'berat'])->nullable()->after('status');
            $table->index('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('report_items', function (Blueprint $table) {
            $table->dropIndex(['difficulty']);
            $table->dropColumn('difficulty');
        });
    }
};
