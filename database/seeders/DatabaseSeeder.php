<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Karma Katoro',
            'email' => 'karmakatoro@gmail.com',
            'password' => Hash::make('passwordd'),
            'gender' => 'm',
            'avatar' => 'avatar-male.png',
            'accred' => '1',
            'type' => 'admin',
            'status' => 'on',
        ]);
    }
}
