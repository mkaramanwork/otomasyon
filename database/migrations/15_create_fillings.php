<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fillings', function (Blueprint $table) {
            $table->id();

            // 🔗 İş emri bağlantısı
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();

            // 🔗 Fiziksel reçete bağlantısı
            $table->foreignId('physical_formula_id')->nullable()->constrained('physical_formulas')->nullOnDelete();

            // 🔗 Dolum hattı bağlantısı
            $table->foreignId('filling_line_id')->nullable()->constrained('production_lines')->nullOnDelete();

            // 📦 Dolum bilgileri
            $table->unsignedInteger('filled_quantity'); // adet
            $table->decimal('volume', 8, 2);            // litre
            $table->decimal('density', 8, 4);           // kg/L

            // 🧪 Hesaplanan malzemeler
            $table->json('calculated_materials')->nullable();

            // 📌 Durum
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');

            // 📝 Notlar
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fillings');
    }
};