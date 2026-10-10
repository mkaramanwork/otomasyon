<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('sds_companies',function(Blueprint $t){$t->id();$t->string('name');$t->text('address')->nullable();$t->string('phone')->nullable();$t->string('fax')->nullable();$t->string('email')->nullable();$t->string('emergency_phone')->nullable();$t->string('logo_path')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('sds_product_families',function(Blueprint $t){$t->id();$t->string('name');$t->string('code')->unique();$t->json('defaults')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('sds_templates',function(Blueprint $t){$t->id();$t->foreignId('company_id')->constrained('sds_companies');$t->string('name');$t->string('code');$t->string('header_color',7)->default('#222222');$t->string('accent_color',7)->default('#eeeeee');$t->string('footer_text')->nullable();$t->string('logo_path')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();$t->unique(['company_id','code']);});
  Schema::create('sds_preparers',function(Blueprint $t){$t->id();$t->string('name');$t->string('title')->nullable();$t->string('certificate_number')->nullable();$t->string('issuer')->nullable();$t->date('issued_at')->nullable();$t->date('expires_at')->nullable();$t->string('email')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::table('sds_formula_profiles',function(Blueprint $t){$t->foreignId('sds_company_id')->nullable()->constrained('sds_companies');$t->foreignId('sds_product_family_id')->nullable()->constrained('sds_product_families');$t->foreignId('sds_template_id')->nullable()->constrained('sds_templates');$t->foreignId('sds_preparer_id')->nullable()->constrained('sds_preparers');});
 }
 public function down(): void {
  Schema::table('sds_formula_profiles',function(Blueprint $t){$t->dropConstrainedForeignId('sds_preparer_id');$t->dropConstrainedForeignId('sds_template_id');$t->dropConstrainedForeignId('sds_product_family_id');$t->dropConstrainedForeignId('sds_company_id');});
  Schema::dropIfExists('sds_preparers');Schema::dropIfExists('sds_templates');Schema::dropIfExists('sds_product_families');Schema::dropIfExists('sds_companies');
 }
};
