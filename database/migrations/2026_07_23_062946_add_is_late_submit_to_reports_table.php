<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {

            if (!Schema::hasColumn('reports', 'is_late_submit')) {
                $table->boolean('is_late_submit')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'is_late_submit')) {
                $table->dropColumn('is_late_submit');
            }
        });
    }
};
