<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('lab_report_settings', function (Blueprint $table) {
            $table->string('stamp_path')->nullable();
            $table->unsignedTinyInteger('logo_width_mm')->default(38);
            $table->unsignedTinyInteger('mark_width_mm')->default(23);
            $table->unsignedTinyInteger('stamp_width_mm')->default(32);
            $table->unsignedTinyInteger('signature_width_mm')->default(30);
        });
    }
    public function down(): void {
        Schema::table('lab_report_settings', function(Blueprint $table) {
            $table->dropColumn(['stamp_path','logo_width_mm','mark_width_mm','stamp_width_mm','signature_width_mm']);
        });
    }
};
