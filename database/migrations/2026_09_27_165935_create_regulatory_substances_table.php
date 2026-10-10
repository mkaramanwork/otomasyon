<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regulatory_substances', function (Blueprint $table) {
            $table->id();

            $table->string('preferred_name')->nullable();

            $table->string('cas_no', 50)->nullable()->unique();
            $table->string('ec_no', 50)->nullable()->index();
            $table->string('index_no', 50)->nullable()->index();

            $table->string('molecular_formula')->nullable();
            $table->string('substance_type')->nullable();

            $table->string('echa_uuid')->nullable()->index();
            $table->text('echa_substance_url')->nullable();

            $table->string('created_from')->nullable();
            $table->string('verification_status')->default('unverified');

            $table->timestamp('last_echa_check_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['cas_no', 'ec_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regulatory_substances');
    }
};