<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('category')->default('Operasional');
            $table->string('title');
            $table->text('message');
            $table->string('image')->nullable();

            $table->string('status')->default('open');

            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolved_image')->nullable(); // Diubah jadi text untuk menampung banyak foto (JSON array)
            $table->text('helpers')->nullable();        // Kolom baru untuk menyimpan array ID teknisi yang membantu
            $table->text('resolved_note')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
