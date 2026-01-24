<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existe para no duplicar
        if (!User::where('email', 'admin@ronald.dev')->exists()) {
            User::create([
                'name' => 'Ronald Admin',
                'email' => 'admin@ronald.dev',
                'password' => Hash::make('password123'), // <--- CAMBIA ESTO DESPUÉS
            ]);
        }
    }
}php artisan db:seed