<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyClosingRequest;
use App\Http\Requests\UpdateDailyClosingRequest;
use App\Models\AgentStock;
use App\Models\DailyClosing;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;

class DailyClosingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDailyClosingRequest $request)
    {
        //
        $request->validate([
            'cashRecevid' => 'required|numeric|min:0',
            'agent_id' => 'required|exists:user,id',
        ]);
        DB::beginTransaction();
        try {
            $agentId = auth()->check() ? auth()->id() : $request->agent_id;
            $agentStock = AgentStock::where('agent_id', $agent_id)
                ->where('quantity', '>=', 0)
                ->get();
            foreach ($agentStock as $stock) {
                $productId = $stock->product_id;
                $product = Product::findOrFail($productId);
                if ($product) {
                    $quantity = $stock->quantity;
                    $product->increment('quantity', $quantity);

                    return response()->json([
                        'status' => true,
                        'message' => 'reingration avec succes',
                    ]);
                    ProductStock::create(

                        [
                            'mouvement' => 'r',
                            'quantity' => $request->quantity,
                            'price' => $product->price,
                            'status' => 'accepted',

                        ]

                    );
                }
                DailyClosing::create([
                'agent_id' => $agentId,
                'total_amount' => $request->total_sales, // Envoyé par le champ caché du modal
                'cash_received' => $request->cashReceived,
                'status' => 'closed'
            ]);

            DB::commit();

            return response()->json([
                'status' => true, 
                'message' => 'Clôture réussie. Le stock a été réintégré au central.'
            ]);

            }
        } catch (Exception $e) {
            DB::rollback(); // Annule tout en cas d'erreur PHP ou SQL
            return response()->json([
                'status' => false, 
                'message' => 'Erreur lors de la clôture : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyClosing $dailyClosing)
    {
        //
    }

    /**php
     * Show the form for editing the specified resource.
     */
    public function edit(DailyClosing $dailyClosing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDailyClosingRequest $request, DailyClosing $dailyClosing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyClosing $dailyClosing)
    {
        //
    }
}
