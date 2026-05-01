<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyClosingRequest;
use App\Http\Requests\UpdateDailyClosingRequest;
use App\Models\AgentStock;
use App\Models\DailyClosing;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Jobs\ProcessStockReturn;
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
    // public function store(StoreDailyClosingRequest $request)
    // {
    //     //
    //     $agentId = auth()->id();

    //     $closing = DailyClosing::create([
    //         'agent_id'=>$agentId,
    //         'cash_received'=>$request->cashReceveid,
    //         'total_amount'=>$request->total_sales,
    //         'status'=>'closed'
    //     ]);
    //     ProcessStockReturn::dispatch($agentId);
    //     return response()->json([
    //         'status'=>true,
    //         'message'=>'Clôture financière enregistrée. Votre stock sera réintégré d\'ici quelques instants.'
    //     ]);

    // }
    public function store(Request $request)
{
    try {
        // Test d'insertion simple
        $closing = DailyClosing::create([
            'agent_id'      => auth()->id(),
            'cash_received' => $request->cashReceived,
            'total_amount'  => $request->total_sales,
            'status'        => 'closed'
        ]);
        ProcessStockReturn::dispatch(auth()->id());
        return response()->json([
            'status' => true,
            'message' => 'Enregistrement réussi en base de données !'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Erreur BD : ' . $e->getMessage()
        ], 500);
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
