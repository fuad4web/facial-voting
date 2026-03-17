<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@votingsystem.com',
            'password' => Hash::make('password'),
            'voter_id' => 'ADMIN-001',
            'is_admin' => true,
        ]);
    }
}
