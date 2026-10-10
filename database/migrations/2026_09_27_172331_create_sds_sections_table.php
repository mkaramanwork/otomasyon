<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sds_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sds_document_id')
                ->constrained('sds_documents')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('section_no');

            $table->string('subsection_no', 20)->nullable();

            $table->string('section_code', 30)->nullable();

            $table->string('title')->nullable();

            $table->longText('generated_content')->nullable();

            $table->longText('final_content')->nullable();

            $table->json('structured_data')->nullable();

            $table->json('source_data')->nullable();

            $table->enum('generation_type', [
                'automatic',
                'template',
                'manual',
                'mixed'
            ])->default('automatic');

            $table->boolean('is_manually_edited')->default(false);

            $table->boolean('is_complete')->default(false);

            $table->boolean('requires_review')->default(false);

            $table->foreignId('last_edited_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('last_edited_at')->nullable();

            $table->text('review_note')->nullable();

            $table->timestamps();

            $table->index([
                'sds_document_id',
                'section_no'
            ], 'sds_section_document_section_idx');

            $table->index([
                'sds_document_id',
                'subsection_no'
            ], 'sds_section_document_subsection_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sds_sections');
    }
};