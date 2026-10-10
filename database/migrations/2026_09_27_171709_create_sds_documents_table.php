<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sds_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('formula_id')
                ->nullable()
                ->constrained('formulas')
                ->nullOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();

            $table->string('document_no')->nullable()->index();

            $table->string('product_name_snapshot');
            $table->string('product_code_snapshot')->nullable();
            $table->string('brand_name_snapshot')->nullable();
            $table->string('formula_name_snapshot')->nullable();

            $table->json('company_snapshot')->nullable();
            $table->json('formula_snapshot')->nullable();

            $table->string('regulation_profile', 100);
            $table->string('jurisdiction', 50)->nullable();

            $table->string('language', 10)->default('tr');

            $table->unsignedInteger('version')->default(1);
            $table->string('revision_no')->nullable();

            $table->date('issue_date')->nullable();
            $table->date('revision_date')->nullable();

            $table->json('regulatory_snapshot')->nullable();
            $table->json('classification_snapshot')->nullable();
            $table->json('label_snapshot')->nullable();

            $table->enum('status', [
                'draft',
                'calculated',
                'review',
                'approved',
                'archived'
            ])->default('draft');

            $table->boolean('calculation_completed')->default(false);
            $table->boolean('regulatory_check_completed')->default(false);

            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('regulatory_checked_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->string('word_file')->nullable();
            $table->string('pdf_file')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'product_id',
                'formula_id'
            ], 'sds_document_product_formula_idx');

            $table->index([
                'regulation_profile',
                'language',
                'status'
            ], 'sds_document_profile_language_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sds_documents');
    }
};