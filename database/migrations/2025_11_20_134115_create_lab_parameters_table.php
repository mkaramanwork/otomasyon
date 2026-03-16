<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
        {
            Schema::create('lab_parameters', function (Blueprint $table) {
                $table->id();
                $table->string('sample_type');      // madeni yağ, gres, antifriz
                $table->string('parameter');        // Viskozite, pH, Yoğunluk
                $table->string('method_tr');        // Metod adı Türkçe
                $table->string('method_en');        // Metod adı İngilizce
                $table->string('method_ru');        // Metod adı Rusça
                $table->string('unit');             // mm²/s, %, pH
                $table->enum('scope', ['TÜRKAK', 'Kapsam Dışı']); // kapsam seçimi
                $table->string('uncertainty')->nullable();
                $table->timestamps();
            });
        }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_parameters');
    }
};
