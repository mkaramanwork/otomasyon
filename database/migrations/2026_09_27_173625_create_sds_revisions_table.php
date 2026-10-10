<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sds_revisions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sds_document_id')
                ->constrained('sds_documents')
                ->cascadeOnDelete();

            $table->unsignedInteger('revision_no');

            $table->string('revision_code')->nullable();

            $table->string('change_type', 100)->nullable();

            $table->text('change_description')->nullable();

            $table->json('affected_sections')->nullable();

            $table->json('old_value')->nullable();

            $table->json('new_value')->nullable();

            $table->json('formula_snapshot_before')->nullable();

            $table->json('formula_snapshot_after')->nullable();

            $table->json('classification_snapshot_before')->nullable();

            $table->json('classification_snapshot_after')->nullable();

            $table->json('regulatory_snapshot_before')->nullable();

            $table->json('regulatory_snapshot_after')->nullable();

            $table->string('reason')->nullable();

            $table->boolean('requires_new_issue')->default(false);

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('changed_at')->nullable();

            $table->timestamps();

            $table->index([
                'sds_document_id',
                'revision_no'
            ], 'sds_revision_document_revision_idx');

            $table->index([
                'change_type',
                'changed_at'
            ], 'sds_revision_type_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sds_revisions');
    }
};