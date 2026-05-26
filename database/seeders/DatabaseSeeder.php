<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->delete();

        User::create([
            'name' => 'Owner Njajan',
            'email' => 'admin@njajan.com',
            'password' => Hash::make('password'), 
            'role' => 'admin',                    
        ]);

        User::create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@njajan.com',
            'password' => Hash::make('password'), 
            'role' => 'kasir',                    
        ]);

        $this->call([
            NjajanSeeder::class,
        ]);
    }
}