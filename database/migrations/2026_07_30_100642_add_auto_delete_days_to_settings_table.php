<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Menambahkan kolom auto_delete_days jika belum ada
            if (!Schema::hasColumn('settings', 'auto_delete_days')) {
                $table->integer('auto_delete_days')->default(0)->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'auto_delete_days')) {
                $table->dropColumn('auto_delete_days');
            }
        });
    }
};
