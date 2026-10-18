<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class MkwepSetupSeeder extends Seeder {
 public function run(): void {
  $this->call([MkwepAccessSeeder::class,MkwepMenuItemSeeder::class]);
 }
}
