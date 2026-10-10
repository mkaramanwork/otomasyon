<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regulatory_import_logs', function (Blueprint $table) {
            $table->id();

            $table->string('source_type', 100);
            $table->string('source_name')->nullable();

            $table->string('regulation_profile', 100)->nullable();
            $table->string('jurisdiction', 50)->nullable();

            $table->string('source_version')->nullable();
            $table->date('source_date')->nullable();

            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();

            $table->string('file_type', 20)->nullable();
            $table->string('file_hash', 64)->nullable()->index();

            $table->enum('status', [
                'uploaded',
                'previewed',
                'processing',
                'completed',
                'failed'
            ])->default('uploaded');

            $table->unsignedInteger('total_rows')->default(0);

            $table->unsignedInteger('new_substances')->default(0);
            $table->unsignedInteger('updated_substances')->default(0);

            $table->unsignedInteger('new_classifications')->default(0);
            $table->unsignedInteger('updated_classifications')->default(0);

            $table->unsignedInteger('unchanged_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);

            $table->json('summary_data')->nullable();
            $table->json('error_data')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('imported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index([
                'source_type',
                'source_version'
            ], 'reg_import_source_version_idx');

            $table->index([
                'regulation_profile',
                'status'
            ], 'reg_import_profile_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regulatory_import_logs');
    }
};