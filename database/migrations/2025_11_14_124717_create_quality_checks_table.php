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
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete(); // ürün adı
            $table->string('batch_number')->nullable(); // batch no
            $table->string('delivery_note_number')->nullable(); // irsaliye no
            $table->date('received_at')->nullable(); // geliş tarihi
            $table->string('supplier_name')->nullable(); // tedarikçi
            $table->enum('kategori', ['katık', 'baz yağ', 'meg']);
            $table->decimal('amount', 10, 2); // miktar
            $table->string('unit')->default('kg'); // birim
            $table->text('notes')->nullable(); // açıklama
            $table->enum('status', ['bekliyor', 'onaylandı', 'reddedildi'])->default('bekliyor'); // durum
            $table->foreignId('checked_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_checks');
    }
};
