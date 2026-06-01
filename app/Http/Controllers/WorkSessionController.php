<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWorkSessionRequest;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use App\Jobs\ProcessStockValidate;
use App\Models\Sale;
use App\Models\Payement;
use App\Jobs\ProcessStockReturn;
use App\Models\AgentStock;
use Illuminate\Support\Facades\DB;
class WorkSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    // On charge les sessions avec la relation 'user' (l'agent) pour éviter les requêtes N+1
    $queueSessions = WorkSession::with('user')
        ->whereIn('status', ['en_attente_cloture', 'stock_valide'])
        ->orderBy('updated_at', 'asc')
        ->get();

    // Retourne la vue principale en lui passant la file d'attente
    return view('pages.journal.index', compact('queueSessions'));
}
public function loadStockModal(WorkSession $session)
    {
        // On charge les assignations et les produits liés pour éviter les requêtes en boucle (Eager Loading)
        $session->load('assignment.product');
        return view('pages.journal._stock_modal', compact('session'));
    }

     public function getClosingStats() {
    try {

        $agentId = auth()->id();
        $session = WorkSession::where('user_id', $agentId)->where('status', 'open')->first();
        
        if (!$session) return response()->json(['error' => 'Session introuvable'], 404);

        $sessionId = $session->id;

        // --- SECTION A : PERFORMANCE (Ce que j'ai vendu) ---
        $totalSales = Sale::where('work_session_id', $sessionId)->sum('total_amount');
        $cashSales = Sale::where('work_session_id', $sessionId)->sum('amount_paid');
        $creditSales = Sale::where('work_session_id', $sessionId)->sum('balance');

        // --- SECTION B : RECOUVREMENT (Ce que j'ai encaissé) ---
        // Paiements pour les ventes faites AUJOURD'HUI
        $payToday = Payement::where('work_session_id', $sessionId)
            ->whereHas('allocations.sale', function($q) use ($sessionId) {
                $q->where('work_session_id', $sessionId);
            })->sum('amount');

        // Paiements pour des dettes ANCIENNES (Recouvrement pur)
        $payOldDebt = Payement::where('work_session_id', $sessionId)
            ->whereHas('allocations.sale', function($q) use ($sessionId) {
                $q->where('work_session_id', '!=', $sessionId);
            })->sum('amount');

        // --- SECTION C : LA CAISSE (Ce que je dois avoir en main) ---
        $totalCashIn = Payement::where('work_session_id', $sessionId)->sum('amount');
        $expectedInHand = $session->opening_cash + $totalCashIn;
        $totalStock = \App\Models\AgentStock::where('user_id', $agentId)->sum('quantity');

        return response()->json([
            'agent_name' => auth()->user()->name,
            'stats' => [
                'ventes_du_jour' => [
                    'total' => number_format($totalSales, 2),
                    'part_cash' => number_format($cashSales, 2),
                    'part_credit' => number_format($creditSales, 2),
                    'stock'=>$totalStock,
                ],
                'encaissements' => [
                    'sur_ventes_du_jour' => number_format($payToday, 2),
                    'sur_dettes_anciennes' => number_format($payOldDebt, 2),
                    'total_cash_entre' => number_format($totalCashIn, 2),
                ],
                'bilan_caisse' => [
                    'fond_initial' => number_format($session->opening_cash, 2),
                    'total_attendu' => number_format($expectedInHand, 2),
                ]
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
public function validateCash(Request $request, $id)
{
    $session = WorkSession::findOrFail($id);

    $request->validate([
        'actual_amount' => 'required|numeric|min:0'
    ]);

    try {
        DB::beginTransaction();

        // 🛠️ Alignement sur ta nouvelle logique de flux de trésorerie
        $totalCashIn = Payement::where('work_session_id', $session->id)->sum('amount');
        
        // Attendu = Ouverture + Tous les paiements encaissés aujourd'hui (Ventes + Dettes)
        $expectedAmount = $session->opening_cash + $totalCashIn;
        $actualAmountReceived = $request->actual_amount;

        // Écart final
        $finalDifference = $expectedAmount - $actualAmountReceived;

        $session->update([
            'closing_cash' => $actualAmountReceived,
            'difference'   => $finalDifference,
            'status'       => 'closed',
            'closed_at'    => now(),
        ]);

        DB::commit();

        if ($request->ajax()) {
            return response()->json([
                'status'  => true,
                'message' => 'Encaissement validé et journal scellé avec succès.',
                'redirect'=> route('journal.show')
            ]);
        }

        return redirect()->route('journal.show')->with('success', 'Journal scellé !');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur validation Cash Journal : " . $e->getMessage());
        return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
    }
}
public function loadCashModal($id)
{
    // 1. Récupérer la session de travail
    $workSession = WorkSession::findOrFail($id);
    $idSession = $workSession->id;

    // --- SECTION A : LES VENTES DU JOUR ---
    $totalSales = Sale::where('work_session_id', $idSession)->sum('total_amount');
    $cashSales  = Sale::where('work_session_id', $idSession)->sum('amount_paid');
    $creditSales = Sale::where('work_session_id', $idSession)->sum('balance');
  

    // --- SECTION B : RECOUVREMENT (Flux de trésorerie entrant) ---
    // Paiements pour les ventes faites AUJOUR'HUI
    $payToday = Payement::where('work_session_id', $idSession)
        ->whereHas('allocations.sale', function($q) use ($idSession) {
            $q->where('work_session_id', $idSession);
        })->sum('amount');

    // Paiements pour des dettes ANCIENNES (Recouvrement pur)
    $payOldDebt = Payement::where('work_session_id', $idSession)
        ->whereHas('allocations.sale', function($q) use ($idSession) {
            $q->where('work_session_id', '!=', $idSession);
        })->sum('amount');

    // --- SECTION C : LA CAISSE (Ce que je dois avoir en main) ---
    $totalCashIn = Payement::where('work_session_id', $idSession)->sum('amount');
    
    // 🛠️ FIX 1 : Remplacement de $session->opening_cash par $workSession->opening_cash (faute de variable)
    $expectedInHand = $workSession->opening_cash + $totalCashIn;

    // 3. Envoyer les variables à ton modal Blade
    return view('pages.journal._cash_modal', [
        'workSession'    => $workSession,
        'session'        => $workSession, 
        'cashTodaySale'  => $cashSales,
        'debtToSale'     => $creditSales,
        'toPay'          => $payToday,
        'debtOldPay'     => $payOldDebt,
        
        // 🛠️ FIX 2 : $totalCashIn doit recevoir $totalCashIn (et non pas $payOldDebt comme tu avais écrit)
        'totalCashIn'    => $totalCashIn, 
        
        'totalSales'     => $totalSales,
        
        // 🛠️ FIX 3 : Définition de $totalPayment qui n'existait pas (cumul des encaissements)
        'totalPayment'   => $totalCashIn, 
        
        'expectedAmount' => $expectedInHand
    ]);
}
public function validateStock(Request $request, WorkSession $session)
{
    try {
        $qtyReturnedPhysical = $request->stock_physique;
         if(empty($qtyReturnedPhysical)){
            return response()->json([
                'status'  => false,
                'message' => 'Aucune donnée de stock physique n\'a été reçue.'
            ]);
        }
        if($session->status !== 'en_attente_cloture'){
            return response()->json([
                'status'=>false,
                'message'=>'Cette session a déjà été traitée ou n\'est pas en attente de clôture.'
            ]);
        }
        ProcessStockValidate::dispatch($session, auth()->id(), $qtyReturnedPhysical);
        return response()->json([
            'status'  => true,
            'message' => 'Le traitement des stocks a été lancé avec succès en arrière-plan. La session passe à l\'étape d\'encaissement.'
        ]);

    }
    catch(\Exception $e){
       return response()->json([
            'status'  => false,
            'message' => 'Erreur lors du lancement du processus : ' . $e->getMessage()
        ]);
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $active = WorkSession::where('user_id', auth()->id())->where('status', 'open')->exists();

        if ($active) {
            return redirect()->route('dashboard.sales'); // Déjà ouvert, on l'envoie travailler !
        }

        return view('session-work.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'opening_cash' => 'required|min:0',
            'note' => 'required',
        ]);
        WorkSession::create([
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_cash' => $request->opening_cash,
            'note' => $request->note ?? 'Ouverture de la Journée',
            'status' => 'open',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Session ouverte !',
            'redirect' => route('dashboard.sales'), // Assure-toi que cette clé existe
        ]);
    }
public function close(Request $request)
{
    // Validation
    $request->validate([
        'cashReceived' => 'required|numeric|min:0',
    ]);

    // On récupère la session
    $workSession = WorkSession::where('user_id', auth()->id())
        ->where('status', 'open')
        ->first(); 

    if (!$workSession) {
        return response()->json(['status' => false, 'message' => 'Aucune session ouverte.'], 404);
    }

    $idSession = $workSession->id;

    // Calculs
    $totalSales = Sale::where('work_session_id', $idSession)->sum('total_amount');
    $totalPayment = Payement::where('work_session_id', $idSession)->sum('amount');

    // Logique : Le système attend (Fonds initial + Ventes réalisées)
    $expectedAmount = $workSession->opening_cash + $totalSales;

    
    $difference = $expectedAmount - $request->cashReceived;

    $closeData = [
        'closed_at'    => now(),
        'closing_cash' => $request->cashReceived,
        'difference'   => $difference,
        'status'       => 'en_attente_cloture'
    ];

    // On lance le Job
    ProcessStockReturn::dispatch(auth()->id(), $closeData, $idSession);

    return response()->json([
        'status'   => true,
        'message'  => 'Session en cours de clôture. Écart : ' . $difference . '$',
        'redirect' => route('sessions.create')
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(WorkSession $workSession)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkSession $workSession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkSessionRequest $request, WorkSession $workSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkSession $workSession)
    {
        //
    }
}
