<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regulatory_classifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('regulatory_substance_id')
                ->constrained('regulatory_substances')
                ->cascadeOnDelete();

            $table->string('regulation_profile', 100);
            $table->string('jurisdiction', 50)->nullable();

            $table->string('source_type', 100)->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_version')->nullable();
            $table->date('source_date')->nullable();

            $table->text('source_reference')->nullable();
            $table->text('source_url')->nullable();

            $table->string('legal_status', 50)->nullable();

            $table->json('hazard_classifications')->nullable();

            $table->string('signal_word', 50)->nullable();

            $table->json('pictograms')->nullable();

            $table->json('hazard_statements')->nullable();
            $table->json('supplemental_hazard_statements')->nullable();

            $table->json('specific_concentration_limits')->nullable();

            $table->json('ate_data')->nullable();

            $table->decimal('m_factor_acute', 12, 4)->nullable();
            $table->decimal('m_factor_chronic', 12, 4)->nullable();

            $table->boolean('reach_registered')->nullable();

            $table->boolean('svhc')->nullable();
            $table->boolean('annex_vi')->nullable();
            $table->boolean('annex_xiv')->nullable();
            $table->boolean('annex_xvii')->nullable();

            $table->string('annex_vi_entry')->nullable();
            $table->string('annex_xiv_entry')->nullable();
            $table->string('annex_xvii_entry')->nullable();

            $table->date('latest_application_date')->nullable();
            $table->date('sunset_date')->nullable();

            $table->longText('authorisation_conditions')->nullable();
            $table->longText('restriction_conditions')->nullable();

            $table->json('dnel_data')->nullable();
            $table->json('pnec_data')->nullable();

            $table->json('toxicology_data')->nullable();
            $table->json('ecotoxicology_data')->nullable();

            $table->boolean('pbt')->nullable();
            $table->boolean('vpvb')->nullable();
            $table->boolean('endocrine_disruptor')->nullable();

            $table->text('pbt_vpvb_notes')->nullable();
            $table->text('endocrine_notes')->nullable();

            $table->json('regulatory_notes')->nullable();

            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();

            $table->timestamp('checked_at')->nullable();

            $table->boolean('is_current')->default(true);
            $table->boolean('is_verified')->default(false);

            $table->timestamps();

            $table->index([
                'regulatory_substance_id',
                'regulation_profile',
                'is_current'
            ], 'reg_class_substance_profile_current_idx');

            $table->index([
                'source_type',
                'source_version'
            ], 'reg_class_source_version_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regulatory_classifications');
    }
};