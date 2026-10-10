<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_sds_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('raw_material_id')
                ->constrained('raw_materials')
                ->cascadeOnDelete();

            $table->foreignId('regulatory_substance_id')
                ->nullable()
                ->constrained('regulatory_substances')
                ->nullOnDelete();

            $table->string('supplier_name')->nullable();
            $table->string('supplier_product_name')->nullable();

            $table->string('sds_language', 10)->default('en');
            $table->string('regulation_profile')->nullable();

            $table->date('supplier_sds_date')->nullable();
            $table->string('supplier_sds_revision')->nullable();
            $table->string('supplier_sds_version')->nullable();

            $table->string('supplier_sds_file')->nullable();

            $table->enum('substance_or_mixture', [
                'substance',
                'mixture'
            ])->nullable();

            $table->string('cas_no', 50)->nullable()->index();
            $table->string('ec_no', 50)->nullable()->index();
            $table->string('index_no', 50)->nullable()->index();

            $table->string('reach_registration_no')->nullable();
            $table->string('kkdik_registration_no')->nullable();

            $table->json('hazard_classifications')->nullable();

            $table->string('signal_word')->nullable();

            $table->json('pictograms')->nullable();

            $table->json('hazard_statements')->nullable();
            $table->json('supplemental_hazard_statements')->nullable();
            $table->json('precautionary_statements')->nullable();

            $table->string('physical_state')->nullable();
            $table->string('colour')->nullable();
            $table->string('odour')->nullable();

            $table->decimal('density', 12, 5)->nullable();
            $table->string('density_unit', 30)->nullable();

            $table->decimal('flash_point', 12, 3)->nullable();
            $table->string('flash_point_unit', 20)->nullable();

            $table->decimal('kinematic_viscosity', 14, 5)->nullable();
            $table->decimal('kinematic_viscosity_temperature', 8, 2)->nullable();
            $table->string('kinematic_viscosity_unit', 30)->nullable();

            $table->json('dnel_data')->nullable();
            $table->json('pnec_data')->nullable();

            $table->json('toxicology_data')->nullable();
            $table->json('ecotoxicology_data')->nullable();

            $table->json('transport_data')->nullable();
            $table->json('regulatory_data')->nullable();

            $table->string('source_name')->nullable();
            $table->date('source_date')->nullable();

            $table->boolean('is_current')->default(true);
            $table->boolean('is_verified')->default(false);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'raw_material_id',
                'is_current'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_sds_profiles');
    }
};