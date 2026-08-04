<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('custom_task_name')->nullable();
            $table->boolean('is_additional')->default(false);
            $table->text('notes')->nullable();
            $table->string('before_image')->nullable();
            $table->string('after_image')->nullable();
            $table->enum('status', ['planned', 'completed', 'pending', 'void'])->default('planned'); // Ditambah 'pending' buat kendala tugas
            $table->text('obstacle_note')->nullable(); // Tambahan buat catatan kendala kalau pending
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_items');
    }
};
