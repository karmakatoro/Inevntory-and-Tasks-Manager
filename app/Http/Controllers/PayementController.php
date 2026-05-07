<?php

namespace App\Http\Controllers;

use App\Models\Payement;
use App\Models\WorkSession;
use App\Jobs\ProcessPayement;
use Illuminate\Http\Request;
use App\Models\User;

class PayementController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'customer_id' => 'required|exists:product_customers,id',
        ]);
     
        $isActiveSession = WorkSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (! $isActiveSession) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur : Aucune session de caisse ouverte.',
            ], 403);
        }
        // 1. Enregistre le paiement brut (Cash-in)
        $payment = Payement::create([
            'amount' => $request->amount,
            'customer_id' => $request->customer_id,
            'payment_method' => $request->payment_method,
            'recorded_by'=>auth()->id(),
            'work_session_id'=>$isActiveSession->id,
            
        ]);
        $user = auth()->id();
        $customer = $request->customer_id;
        
        // 2. SECRET SENIOR : On lance le Job en arrière-plan
        ProcessPayement::dispatch($payment->id, $customer, $request->amount,$user);

        return response()->json([
            'status' => 'success',
            'message' => 'Le paiement est en cours de traitement par le système.',
        ]);
    }
}
