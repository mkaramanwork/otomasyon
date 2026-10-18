<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MkwepAccessSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('mkwep_access.modules') as $module => $meta) {
            foreach (config('mkwep_access.actions') as $action => $label) {
                $newName = "mkwep.$module.$action";
                $oldName = "alkim.$module.$action";
                $old = Permission::where('name', $oldName)->where('guard_name', 'web')->first();
                $new = Permission::where('name', $newName)->where('guard_name', 'web')->first();
                if ($old && !$new) {
                    // V1 kullaniminda mevcut kullanici yetki baglantilarini koru.
                    $old->update(['name' => $newName]);
                } elseif (!$new) {
                    Permission::findOrCreate($newName, 'web');
                }
            }
        }
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
