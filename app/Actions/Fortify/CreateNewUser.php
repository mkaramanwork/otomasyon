<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateNewUser
{
    public function create(array $input)
    {
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Default rol atama
        $role = Role::where('name', 'user')->first();
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }
}
