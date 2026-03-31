<?php

namespace App\Http\Controllers;

use App\Models\AgentStock;
use App\Models\Assignment;
use App\Models\Product;
use App\Models\TemporaryReservation;
use App\Models\User; // CORRECTION : Vérifie bien le nom du modèle (Temporary au lieu de Tempory)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // AJOUT : Indispensable pour Session::getId()
use Illuminate\Support\Facades\Session;      // AJOUT : Indispensable pour DB::raw()

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $cart = session()->get('assign_cart', []);
        $productId = $request->product_id;
        //Vérifier si le produit est déjà dans le panier
        if (isset($cart[$productId])) {
            return response()->json([
                'status' => false,
                'message' => "Ce produit est déjà dans votre panier d'attribution.",
            ], 422);
        }
        // 1. Récupération du produit
        $product = Product::findOrFail($request->product_id);
        $qtyRequested = $request->quantity ?? 0;

        // 2. Vérification du stock disponible (ton attribut magique)
        if ($product->available_stock < $qtyRequested) {
            return response()->json([
                'success' => false,
                'message' => "Désolé, seulement {$product->available_stock} articles sont disponibles.",
            ], 422);
        }

        // 3. Réservation en BDD

        $currentSessionId = Session::getId();
        try {
            $reservation = TemporaryReservation::updateOrCreate(
                [
                    'session_id' => $currentSessionId,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $qtyRequested,
                    'expires_at' => now()->addMinutes(2),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Erreur lors de la réservation en base de données.',
                ], 500
            );
        }

        $cart[$product->id] = [
            'name' => $product->name,
            'quantity' => $qtyRequested,
            'price' => $product->price,
            'photo' => $product->photo,
        ];
        // 5. Sauvegarde et réponse
        session()->put('assign_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => "Produit ajouté au panier d'attribution",
            'cart_count' => count($cart),
        ]);
    }

    // --- INCREMENTER (+1) ---
    public function incrementer(Request $request)
    {
        TemporaryReservation::clearExpired();

        $productId = $request->product_id;
        $cart = session()->get('assign_cart', []); // CRITIQUE : Il manquait cette ligne
        $product = Product::findOrFail($productId);

        $currentSession = session()->getId();

        $maReservation = TemporaryReservation::where('session_id', $currentSession)
            ->where('product_id', $productId)
            ->first();

        // Vérification expiration
        if (! $maReservation && isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('assign_cart', $cart);

            return response()->json(['status' => false, 'message' => 'Session expirée, produit remis en rayon.']);
        }

        // Vérification stock disponible
        if ($product->available_stock <= 0) {
            return response()->json([
                'status' => false,
                'message' => "Stock insuffisant : {$product->available_stock} restant.",
            ]);
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
            session()->put('assign_cart', $cart);

            // Mise à jour BDD
            $maReservation->increment('quantity', 1);

            // On rafraîchit le modèle pour avoir le bon chiffre après l'incrément
            $nouveauStock = $product->refresh()->available_stock;

            return response()->json([
                'status' => true,
                'message' => "Stock restant : {$nouveauStock} pour {$product->name}",
            ]);
        }
    }

    // --- DECREMENTER (-1) ---
    public function decrementer(Request $request)
    {
        $productId = $request->product_id;
        $cart = session()->get('assign_cart', []);
        $product = Product::findOrFail($productId);
        $currentSession = session()->getId();

        if (isset($cart[$productId]) && $cart[$productId]['quantity'] > 1) {
            $cart[$productId]['quantity']--;
            session()->put('assign_cart', $cart);

            TemporaryReservation::where('session_id', $currentSession)
                ->where('product_id', $productId)
                ->decrement('quantity', 1);

            $nouveauStock = $product->refresh()->available_stock;

            return response()->json([
                'status' => true,
                'message' => "Stock restant : {$nouveauStock}",
            ]);
        }
    }

    public function remove(Request $request)
    {
        $productId = $request->product_id;
        $cart = session()->get('assign_cart', []);
        $currentSession = session()->getId();

        // 1. Nettoyage en Base de Données (Priorité)
        // On libère le stock physiquement avant de toucher à la session
        TemporaryReservation::where('session_id', $currentSession)
            ->where('product_id', $productId)
            ->delete();

        // 2. Nettoyage de la Session
        if (isset($cart[$productId])) {
            unset($cart[$productId]);

            if (empty($cart)) {
                session()->forget('assign_cart');
                // Optionnel : session()->forget('assignment_expires_at');
            } else {
                session()->put('assign_cart', $cart);
            }
        }

        // 3. Récupération du nouveau stock disponible pour l'affichage
        $product = Product::find($productId);
        $nouveauStock = $product ? $product->available_stock : 0;

        return response()->json([
            'status' => true,
            'message' => 'Produit retiré. Stock de '.($product->name ?? 'produit').' libéré.',
            'new_available_stock' => $nouveauStock,
            'cart_empty' => empty($cart),
        ]);
    }

    public function fetchCart()
    {
        // 1. Nettoyage physique des expirations en BDD
        TemporaryReservation::clearExpired();

        // 2. Synchronisation : On récupère les IDs restants en BDD pour cette session
        $activeReservationIds = TemporaryReservation::where('session_id', session()->getId())
            ->pluck('product_id')
            ->toArray();

        // 3. On nettoie la SESSION PHP : On ne garde que ce qui existe encore en BDD
        $cart = session()->get('assign_cart', []);
        foreach ($cart as $id => $item) {
            if (! in_array($id, $activeReservationIds)) {
                unset($cart[$id]); // Si plus en BDD, on l'enlève du panier
            }
        }

        // On met à jour la session nettoyée
        if (empty($cart)) {
            session()->forget('assign_cart');
        } else {
            session()->put('assign_cart', $cart);
        }

        // 4. On récupère les agents pour la vue
        $agents = User::where('type', 'user')->get();

        // 5. Génération du HTML (le produit expiré n'y sera plus !)
        $html = view('pages.product-stock.cart-content', compact('agents'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
        ]);
    }

    public function validerLot(Request $request)
    {
        $agentId = $request->agent_id;
        $cart = session()->get('assign_cart', []);
        if (! $agentId) {
            return response()->json([
                'status' => false,
                'message' => 'Veuillez sélectionner un agent.',
            ], 422);
        }
        if (empty($cart)) {
            return response()->json([
                'status' => false,
                'message' => 'Le panier est vide.',
            ], 422);
        }
        try {
            DB::beginTransaction();

            foreach ($cart as $productId => $item) {
                // 1. Verrouillage et récupération du produit central
                $product = Product::lockForUpdate()->find($productId);

                if (! $product || $product->quantity < $item['quantity']) {
                    throw new \Exception('Stock insuffisant pour le produit : '.($product->name ?? $productId));
                }

                // 2. Décrémentation du stock central
                $product->decrement('quantity', $item['quantity']);

                // 3. Mise à jour du stock de l'agent (ACCUMULATION)
                // On utilise DB::raw pour dire à SQL de faire : ancien_stock + nouveau_stock
                AgentStock::updateOrCreate(
                    [
                        'user_id' => $agentId,
                        'product_id' => $productId,
                    ],
                    [
                        'quantity' => DB::raw('quantity + '.$item['quantity']),
                    ]
                );

                // 4. Création de la trace dans l'historique
                Assignment::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $agentId,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                ]);
            }

            // 5. Nettoyage après la boucle
            TemporaryReservation::where('session_id', session()->getId())->delete();
            session()->forget('assign_cart');

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Attribution réussie ! Le stock de l'agent a été mis à jour.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erreur : '.$e->getMessage(),
            ], 500);
        }

    }
}
