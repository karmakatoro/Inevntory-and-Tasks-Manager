<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        static $categoriesCache = null;

        // Charger une seule fois (optimisation énorme pour 15k)
        if ($categoriesCache === null) {
            $categoriesCache = [
                'Électroniques' => ['Téléphones', 'Chargeurs', 'Casques audio'],
                'Électroménagers' => ['Réfrigérateurs', 'Mixeurs'],
                'Maison et Cuisine' => ['Poêles', 'Balais'],
                'Alimentation' => ['Riz', 'Boissons'],
                'Cosmétique et Beauté' => ['Parfums', 'Savons'],
                'Mode et Habillement' => ['Chaussures', 'T-shirts'],
                'Construction et Quincaillerie' => ['Ampoules', 'Clous'],
                'Bureau et Fournitures' => ['Cahiers', 'Stylos'],
                'Santé et Pharmacie' => ['Désinfectants'],
                'Automobile et Moto' => ['Batteries'],
                'Informatique' => ['Claviers', 'Souris'],
            ];
        }

        $categoryName = fake()->randomElement(array_keys($categoriesCache));
        $subcategory = fake()->randomElement($categoriesCache[$categoryName]);

        $name = fake()->words(3, true);

        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? 1,

            'product_category_id' => ProductCategory::query()
                ->where('name', $categoryName)
                ->value('id'),

            'subcategories' => [$subcategory],

            'name' => $name,

            'slug' => Str::slug($name . '-' . Str::random(8)), // ✅ FIX UNIQUE

            'description' => fake()->sentence(12),

            'photo' => 'https://picsum.photos/seed/' . Str::random(10) . '/600/600',

            'gallery' => [
                'https://picsum.photos/seed/' . Str::random(10) . '/600/600',
                'https://picsum.photos/seed/' . Str::random(10) . '/600/600',
            ],

            'price' => fake()->numberBetween(2, 500),

            'quantity' => fake()->numberBetween(1, 200),

            'cmp' => '1',

            'status' => 'on',
        ];
    }
}