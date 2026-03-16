<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['karışım', 'sıvı', 'toz']);
            $table->text('description')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('packaging_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0); // %
            $table->decimal('vat', 5, 2)->default(20); // %
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};