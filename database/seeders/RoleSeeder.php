<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Roller varsa tekrar ekleme
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'üretimmüdürü']);
        Role::firstOrCreate(['name' => 'harmanlama']);
        Role::firstOrCreate(['name' => 'peketleme']);
        Role::firstOrCreate(['name' => 'şef']);
        Role::firstOrCreate(['name' => 'genelmüdür']);
    }
}
