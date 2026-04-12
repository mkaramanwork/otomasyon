<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sample_acceptance_additions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_acceptance_id')->constrained()->onDelete('cascade'); // numune
            $table->string('sample_name');   // numune adı
            $table->string('batch_no');      // batch no
            $table->string('tank_no')->nullable(); // ✅ tank no
            $table->text('reason');          // ilave nedeni
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // kaydeden kullanıcı
            $table->json('parameters')->nullable(); // seçilen parametreler (JSON listesi)
            $table->text('nonconformity_result')->nullable(); // uygunsuz çıkan sonuç
            $table->text('nonconformity_fix')->nullable();   // uygunsuzluk nasıl düzeltildi
            $table->timestamp('reason_date')->nullable();    // ilave tarih/saat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_acceptance_additions');
    }
};
