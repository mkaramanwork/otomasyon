<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Roller varsa tekrar ekleme
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'laboratuvarpersoneli', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'numunekabulveraporlamapersoneli', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'laboratuvarmüdürü', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'üretimmüdürü', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'harmanlama', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'peketleme', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'şef', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'genelmüdür', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'satis', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'uretim', 'guard_name' => 'web']);
    }
}
