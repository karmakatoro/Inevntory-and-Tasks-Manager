<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $categories = [

            // Catégories principales
            'Électroniques',
            'Électroménagers',
            'Maison et Cuisine',
            'Alimentation',
            'Cosmétique et Beauté',
            'Mode et Habillement',
            'Construction et Quincaillerie',
            'Bureau et Fournitures',
            'Santé et Pharmacie',
            'Agriculture et Élevage',
            'Automobile et Moto',
            'Informatique',

            // Sous-catégories
            'Téléphones',
            'Ordinateurs',
            'Tablettes',
            'Chargeurs',
            'Casques audio',
            'Télévisions',

            'Réfrigérateurs',
            'Congélateurs',
            'Mixeurs',
            'Ventilateurs',

            'Poêles',
            'Casseroles',
            'Balais',
            'Assiettes',
            'Tables',

            'Boissons',
            'Riz',
            'Huile',
            'Biscuits',

            'Parfums',
            'Savons',
            'Crèmes',
            'Shampooings',

            'T-shirts',
            'Pantalons',
            'Chaussures',
            'Montres',

            'Ciment',
            'Peinture',
            'Ampoules',
            'Clous',

            'Cahiers',
            'Stylos',
            'Imprimantes',

            'Médicaments',
            'Masques',
            'Désinfectants',

            'Semences',
            'Engrais',
            'Outils agricoles',

            'Pneus',
            'Batteries',
            'Huiles moteur',

            'Claviers',
            'Souris',
            'RAM',
            'Disques durs',
        ];

        foreach ($categories as $category) {

            ProductCategory::create([
                'name' => $category,
                'status' => 'on',
            ]);
        }
    }
    
}
