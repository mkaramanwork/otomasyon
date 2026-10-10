<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sds_formula_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formula_id')
                ->unique()
                ->constrained('formulas')
                ->cascadeOnDelete();

            $table->string('product_type', 100)
                ->default('lubricant');

            $table->text('identified_use')
                ->nullable();

            $table->text('uses_advised_against')
                ->nullable();

            $table->json('first_aid')
                ->nullable();

            $table->json('fire_fighting')
                ->nullable();

            $table->json('accidental_release')
                ->nullable();

            $table->json('handling_storage')
                ->nullable();

            $table->json('exposure_controls')
                ->nullable();

            $table->json('physical_properties')
                ->nullable();

            $table->json('stability_reactivity')
                ->nullable();

            $table->json('toxicology')
                ->nullable();

            $table->json('ecology')
                ->nullable();

            $table->json('disposal')
                ->nullable();

            $table->json('transport')
                ->nullable();

            $table->json('regulatory')
                ->nullable();

            $table->json('revision')
                ->nullable();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sds_formula_profiles');
    }
};
