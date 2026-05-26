<?php

namespace App\Http\Controllers;

use App\Models\AgentStock;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductAssignmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Assignment::with(['receiver', 'sender'])
                ->select([
                    'reference_bon',
                    'sender_id',
                    'receiver_id',
                    'status',
                    'created_at',
                    DB::raw('COUNT(id) as total_distinct_products'),
                    DB::raw('SUM(quantity) as total_quantity'),
                ])
                ->groupBy('reference_bon', 'sender_id', 'receiver_id', 'status', 'created_at');
            $user = auth()->user();
            // if (! $user->hasRole('admin')) {
            //     $query->where('receiver_id', $user->id);
            // }
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            if ($user->hasRole('admin') && $request->filled('agent_id')) {
                $query->where('receiver_id', $request->agent_id);
            }
            $query->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="form-check-input row-checkbox" value="'.$row->reference_bon.'">';
                })
                ->editColumn('reference_bon', function ($row) {
                    return '<span class="badge bg-nav-light text-dark font-13 p-1"><i class="mdi mdi-receipt-text-outline me-1"></i>'.$row->reference_bon.'</span>';
                })
                ->addColumn('sender', function ($row) {
                    return $row->sender ? $row->sender->name : 'Système';
                })
                ->addColumn('receiver', function ($row) {
                    return $row->receiver ? $row->receiver->name : '<span class="text-muted">Non spécifié</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->editColumn('status', function ($row) {
                    switch ($row->status) {
                        case 'en_attente':
                            return '<span class="badge bg-warning-lighten text-warning px-2 py-1">● En attente</span>';
                        case 'approuve':
                            return '<span class="badge bg-success-lighten text-success px-2 py-1">● Approuvé</span>';
                        case 'rejete':
                            return '<span class="badge bg-danger-lighten text-danger px-2 py-1">● Rejeté</span>';
                        default:
                            return '<span class="badge bg-secondary-lighten text-secondary px-2 py-1">'.$row->status.'</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button type="button" class="btn btn-sm btn-info btn-view-details" 
                            data-bon="'.$row->reference_bon.'" 
                            data-sender="'.($row->sender ? $row->sender->name : 'Admin').'" 
                            data-receiver="'.($row->receiver ? $row->receiver->name : 'Agent').'">
                            <i class="mdi mdi-eye me-1"></i> Détails
                        </button>
                    ';
                })
                ->rawColumns(['checkbox', 'reference_bon', 'status', 'action'])
                ->make(true);

        }

        return view('pages.product-stock.assign');

    }
   
public function getDetailsBon($reference_bon)
{
    $user = auth()->user();
    
    // 1. On récupère TOUTES les lignes liées à ce bon (SANS GROUPBY ici, pour avoir chaque produit)
    $query = Assignment::with('product')->where('reference_bon', $reference_bon);

    // // Sécurité Spatie
    // if (!$user->hasRole('admin')) {
    //     $query->where('receiver_id', $user->id);
    // }

    $assignments = $query->get();

    if ($assignments->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Accès refusé ou bon introuvable.'], 403);
    }

    // 2. L'ASTUCE SENIOR : Sécuriser les noms des colonnes selon ton modèle et ta migration
    $formattedData = $assignments->map(function($item) {
        return [
            'product_name'      => $item->product ? $item->product->name : 'Produit inconnu',
            // On utilise ?? 0 pour éviter le crash si une valeur est nulle en base de données
            'quantity'          => $item->quantity ?? $item->quantite_envoyee ?? 0, 
            'quantity_received' => $item->quantity_received ?? '-',
            'quantity_returned' => $item->quantity_returned ?? 0,
        ];
    });

    return response()->json(['success' => true, 'data' => $formattedData]);
}

    //
    public function acceptAssignment(Request $request, $assignmentId)
    {
        DB::beginTransaction();
        try {
            $assignment = Assignment::where('id', $assignmentId)
                ->where('receiver_id', auth()->id())
                ->where('status', 'en_attente')
                ->firstOrFail();
            $agentSessionActive = WorkSession::where('user_id', auth()->id())
                ->where('status', 'open')
                ->first();

            // 1. Mettre à jour l'affectation
            $assignment->update([
                'quantity_received' => $assignment->quantity,
                'status' => 'approuve',
                'work_session_id' => $agentSessionActive->id,
                'accepted_at' => now(),
            ]);

            // 2. Transférer ENFIN dans son stock réel pour la vente
            AgentStock::updateOrCreate([
                'user_id' => $assignment->receiver_id,
                'product_id' => $assignment->product_id,
            ], [
                'quantity' => DB::raw("quantity + {$assignment->quantity}"),
                'price' => $request->price ?? 0,
            ]);

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Stock accepté et ajouté à votre boutique !']);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'message' => 'Erreur : '.$e->getMessage()], 500);
        }
    }
  
    
}
