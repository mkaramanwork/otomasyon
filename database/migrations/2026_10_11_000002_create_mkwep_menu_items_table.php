<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('mkwep_menu_items', function(Blueprint $table) {
   $table->id(); $table->string('item_key',110)->unique();
   $table->string('parent_key',110)->nullable()->index();
   $table->string('item_type',20)->default('link'); // header, parent, link
   $table->string('label',160); $table->string('module_key',100)->nullable();
   $table->string('route_name',160)->nullable(); $table->json('route_params')->nullable();
   $table->string('icon',110)->nullable(); $table->unsignedInteger('sort_order')->default(0);
   $table->boolean('is_active')->default(true); $table->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('mkwep_menu_items'); }
};
