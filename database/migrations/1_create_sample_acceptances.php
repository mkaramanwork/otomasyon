<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_acceptances', function (Blueprint $table) {
            $table->id();

            // Üretimden otomatik gelenler
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // ürün bilgisi
            $table->string('company_name'); // firma adı
            $table->string('batch_no'); // numune kodu

            // Laboratuvarın girdiği alanlar
            $table->string('country')->nullable(); // ülke seçimi
            $table->boolean('uncertainty')->default(false); // ölçüm belirsizliği
            $table->boolean('report_request')->default(false); // rapor talebi
            $table->string('report_no')->nullable(); // rapor numarası
            $table->unsignedBigInteger('request_no'); // talep no (otomatik artacak)

            // Numune bilgileri
            $table->string('sample_name')->nullable(); // numune adı
            $table->enum('sample_type', ['madeni yağ','antifriz','gres']); // numune türü
            $table->text('description')->nullable(); // kısa açıklama
            $table->enum('taken_place', ['üretim hattı','ham madde','harmanlama','dolum numunesi','müşteri']); // alındığı yer
            $table->string('sampling_person')->nullable(); // numuneyi alan kişi adı
            $table->enum('taken_by', ['üretim çalışanı','müşteri']); // numuneyi alan kişi tipi
            $table->dateTime('taken_at'); // alınma tarihi ve saati
            $table->integer('amount')->default(300); // numune miktarı (ml)
            $table->enum('protection', ['korumasız','bilinmiyor','soğuk taşıma','mühürlü']); // koruma işlemi

            // Durum
            $table->enum('status', ['pending','accepted','rejected'])->default('pending');

            // ✅ Numuneyi kabul eden kişi
            $table->foreignId('accepted_by')->nullable()->constrained('users')->onDelete('set null');

            // ✅ Rapor meta alanları
            $table->date('report_date')->nullable(); // rapor tarihi
            $table->text('report_description')->nullable(); // rapor açıklaması
            $table->json('report_images')->nullable(); // rapor resimleri (JSON array)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_acceptances');
    }
};