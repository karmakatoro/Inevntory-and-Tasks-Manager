<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions =[
            // Stock
            'creer-produits',
            'approvisionner-depot',
            'assigner-stock-agent',
            'accepter-stock-recu',
            'valider-retour-stock',
            // Ventes & Paiements
            'effectuer-vente',
            'enregistrer-paiement',
            'voir-dettes-clients',
            // Sessions & Clôtures
            'ouvrir-session',
            'demander-cloture',
            'valider-cloture-finance',
            // Rapports
            'voir-rapports-vendeurs',
            'voir-rapports-globaux'

        ];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
        // 3. Création des Rôles et Attribution des Permissions

        // --- RÔLE AGENT (Boutique / Caissier) ---
        $agentRole = Role::findOrCreate('Agent');
        $agentRole->givePermissionTo([
            'accepter-stock-recu',
            'effectuer-vente',
            'enregistrer-paiement',
            'ouvrir-session',
            'demander-cloture',
            'voir-rapports-vendeurs'
        ]);
        // --- RÔLE STOCK MANAGER ---
        $stockManagerRole = Role::findOrCreate('Stock Manager');
        $stockManagerRole->givePermissionTo([
            'creer-produits',
            'approvisionner-depot',
            'assigner-stock-agent',
            'valider-retour-stock'
        ]);
        // --- RÔLE SUPER ADMIN ---
        $adminRole = Role::findOrCreate('Admin');
        $adminRole->givePermissionTo(Permission::all()); // L'admin a TOUT
        $users = User::all();
        foreach ($users as $user) {
            $user->syncRoles([]);
            if($user->type ==='admin'){
                $user->update(['accred'=>'1']);
                $user->assignRole($adminRole);
            }
            elseif ($user->type==='user') {
                 if($user->accred ==='2'){
                    $user->assignRole($stockManagerRole);
                 }
                else {
                    $user->update(['accred'=>'3']);
                    $user->assignRole($agentRole);
                }
            
            }
        }
    }
}
