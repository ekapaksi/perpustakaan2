<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'Admin',
            'alamat' => 'Jakarta',
            'email' => 'admin@example.com',
            'image' => 'profil-pic/default.jpg',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'is_active' => 1,
        ]);
    }
}
