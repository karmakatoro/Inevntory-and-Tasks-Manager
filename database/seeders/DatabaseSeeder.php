<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Karma Katoro',
        //     'phone' => '+243 995 010 362',
        //     'email' => 'karmakatoro@gmail.com',
        //     'password' => Hash::make('passwordd'),
        //     'gender' => 'm',
        //     'photo' => 'avatar-male.png',
        //     'accred' => '1',
        //     'type' => 'admin',
        //     'status' => 'on',
        // ]);
        // \App\Models\User::factory()->create([
        //     'name' => 'Tresor kitwanda',
        //     'phone' => '+243 971268803',
        //     'email' => 'trezkit00@gmail.com',
        //     'password' => Hash::make('passwordd'),
        //     'gender' => 'm',
        //     'photo' => 'avatar-7.jpg',
        //     'accred' => '1',
        //     'type' => 'admin',
        //     'status' => 'on',
        // ]);
        $this->call([
        ProductCategorySeeder::class,
    ]);
     $this->call([
        ProductSeeder::class,
    ]);
    }
    
}
