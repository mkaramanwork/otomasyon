<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formula_id')->constrained()->cascadeOnDelete();
            $table->foreignId('production_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('batch_number')->unique();
            $table->string('unit', 10)->default('kg');
            $table->decimal('quantity_kg', 10, 2)->default(0);
            $table->dateTime('planned_at');

            $table->enum('status', [
                'planned',
                'in_production',
                'produced',
                'in_filling',
                'filled',
                'cancelled'
            ])->default('planned');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};