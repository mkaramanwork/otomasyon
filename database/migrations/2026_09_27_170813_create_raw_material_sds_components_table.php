<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('raw_material_sds_components')) {

            Schema::create('raw_material_sds_components', function (Blueprint $table) {

                $table->id();

                $table->foreignId('raw_material_sds_profile_id')
                    ->constrained('raw_material_sds_profiles')
                    ->cascadeOnDelete();

                $table->foreignId('regulatory_substance_id')
                    ->nullable()
                    ->constrained('regulatory_substances')
                    ->nullOnDelete();

                $table->string('chemical_name');

                $table->string('cas_no', 50)
                    ->nullable()
                    ->index();

                $table->string('ec_no', 50)
                    ->nullable()
                    ->index();

                $table->string('index_no', 50)
                    ->nullable()
                    ->index();

                $table->decimal('concentration_min', 12, 6)
                    ->nullable();

                $table->decimal('concentration_max', 12, 6)
                    ->nullable();

                $table->string('concentration_unit', 20)
                    ->default('%');

                $table->json('hazard_classifications')
                    ->nullable();

                $table->json('h_statements')
                    ->nullable();

                $table->json('specific_concentration_limits')
                    ->nullable();

                $table->json('ate_data')
                    ->nullable();

                $table->decimal('m_factor_acute', 12, 4)
                    ->nullable();

                $table->decimal('m_factor_chronic', 12, 4)
                    ->nullable();

                $table->json('regulatory_notes')
                    ->nullable();

                $table->string('source_section', 20)
                    ->default('3');

                $table->unsignedInteger('source_page')
                    ->nullable();

                $table->text('supplier_note')
                    ->nullable();

                $table->boolean('is_disclosed')
                    ->default(true);

                $table->boolean('is_verified')
                    ->default(false);

                $table->timestamps();

                $table->index(
                    [
                        'raw_material_sds_profile_id',
                        'regulatory_substance_id'
                    ],
                    'rm_sds_component_profile_substance_idx'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_sds_components');
    }
};