<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lab_report_settings', function (Blueprint $table) {
            $table->id();
            $table->string('laboratory_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('accreditation_number')->nullable();
            $table->boolean('accreditation_mark_enabled')->default(false);
            $table->string('logo_path')->nullable();
            $table->string('mark_path')->nullable();
            $table->foreignId('laboratory_manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('report_preparer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_code')->default('FRM.044/22.09.2025-11');
            $table->timestamps();
        });
        Schema::create('lab_report_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('file_path');
            $table->timestamps();
        });
        Schema::create('lab_report_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_acceptance_id')->unique()->constrained('sample_acceptances')->cascadeOnDelete();
            $table->foreignId('approved_by')->constrained('users');
            $table->string('content_hash', 64);
            $table->timestamp('approved_at');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('lab_report_approvals');
        Schema::dropIfExists('lab_report_signatures');
        Schema::dropIfExists('lab_report_settings');
    }
};
