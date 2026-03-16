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
Schema::create('uygunsuzluk', function (Blueprint $table) {
    $table->id();
    $table->string('bolum')->nullable();
    $table->string('tanim')->nullable();
    $table->date('bildirim_tarihi')->nullable();
    $table->string('uy_sinif')->nullable();
    $table->string('durum')->nullable();
    $table->string('kaynak')->nullable();
    $table->string('uy_aciklama')->nullable();
    $table->string('yapilacak_faaliyet')->nullable();
    $table->string('kaplam_kok')->nullable();
    $table->string('duzeltici_faaliyet')->nullable();
    $table->string('tespit_eden')->nullable();
    $table->string('falliyet_sorumlu')->nullable();
    $table->date('planlanan_tarih')->nullable();
    $table->string('faaliyet_degerlendirme')->nullable();
    $table->string('faaliyet_aciklama')->nullable();
    $table->string('faaliyet_kapatan')->nullable();
    $table->date('faaliyet_tarih')->nullable();
    $table->string('etkinlik_degerlendirme')->nullable();
    $table->string('etkinlik_aciklama')->nullable();
    $table->string('etkinlik_kapatan')->nullable();
    $table->date('etkinlik_tarih')->nullable();
    $table->string('file_name')->nullable();
    $table->timestamp('uploaded_on')->useCurrent();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uygunsuzluk');
    }
};
