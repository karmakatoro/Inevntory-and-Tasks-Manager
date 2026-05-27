<?php

namespace App\Http\Controllers;

use App\Models\AgentStock;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Models\WorkSession;
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

            if (! $user->hasRole('Admin')) {
                $query->where('receiver_id', $user->id);
            }
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            if ($user->hasRole('Admin') && $request->filled('agent_id')) {
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
                ->addColumn('action', function ($row) use ($user) {
                    $senderName = $row->sender ? addslashes($row->sender->name) : 'Admin';
                    $receiverName = $row->receiver ? addslashes($row->receiver->name) : 'Agent';

                    // 1. On prépare le bouton principal selon le rôle
                    $mainButton = '';
                    if (! $user->hasRole('Admin')) {
                        // L'Agent voit "Confirmer" en bouton principal
                        $mainButton = '
            <button type="button" class="btn btn-sm btn-soft-success btn-action-assign" 
                data-bon="'.$row->reference_bon.'" data-id="'.$row->id.'" data-action="confirmer" title="Confirmer la réception">
                <i class="mdi mdi-check-all me-1"></i> Confirmer
            </button>
            <button type="button" class="btn btn-sm btn-soft-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                <i class="mdi mdi-chevron-down"></i>
            </button>';
                    } else {
                        // L'Admin voit "Détails" directement en bouton principal (style neutre/pro)
                        $mainButton = '
            <button type="button" class="btn btn-sm btn-soft-info btn-view-details" 
                data-bon="'.$row->reference_bon.'" data-sender="'.$senderName.'" data-receiver="'.$receiverName.'" title="Voir les détails">
                <i class="mdi mdi-eye-outline me-1"></i> Détails
            </button>
            <button type="button" class="btn btn-sm btn-soft-info dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                <i class="mdi mdi-chevron-down"></i>
            </button>';
                    }

                    // 2. On construit le menu déroulant avec les restrictions de sécurité
                    $dropdownItems = '';

                    // Le bouton "Voir les détails" va dans le dropdown uniquement pour l'Agent (car déjà dehors pour l'Admin)
                    if (! $user->hasRole('Admin')) {
                        $dropdownItems .= '
            <button type="button" class="dropdown-item btn-view-details text-info py-2" 
                data-bon="'.$row->reference_bon.'" data-sender="'.$senderName.'" data-receiver="'.$receiverName.'">
                <i class="mdi mdi-eye-outline me-2 font-16 align-middle"></i> Voir les détails
            </button>';
                    }

                    // Seul l'Admin a le droit d'annuler un bon de sortie
                    if ($user->hasRole('Admin')) {
                        $dropdownItems .= '
            <button type="button" class="dropdown-item btn-action-assign text-danger py-2" 
                data-bon="'.$row->reference_bon.'" data-action="annuler">
                <i class="mdi mdi-close-circle-outline me-2 font-16 align-middle"></i> Annuler le bon
            </button>';
                    }

                    // 3. On assemble le tout dans le bloc HTML final
                    return '
        <div class="btn-group font-13">
            '.$mainButton.'
            <div class="dropdown-menu dropdown-menu-animated dropdown-menu-end shadow border-0" style="min-width: 160px;">
                '.$dropdownItems.'
            </div>
        </div>
    ';
                })
                ->rawColumns(['checkbox', 'reference_bon', 'status', 'action'])
                ->make(true);

        }
        // 🌟 Redirection vers la bonne vue selon le rôle Spatie
        $user = auth()->user();

        if ($user->hasRole('Admin')) {

            return view('pages.product-stock.assign');
        }

        return view('pages.stock-agent.assign');

    }

    public function getDetailsBon($reference_bon)
    {
        $user = auth()->user();

        // 1. On récupère TOUTES les lignes liées à ce bon (SANS GROUPBY ici, pour avoir chaque produit)
        $query = Assignment::with('product')->where('reference_bon', $reference_bon);

        // Sécurité Spatie
        if (!$user->hasRole('Admin')) {
            $query->where('receiver_id', $user->id);
        }

        $assignments = $query->get();

        if ($assignments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé ou bon introuvable.'], 403);
        }

        // 2. L'ASTUCE SENIOR : Sécuriser les noms des colonnes selon ton modèle et ta migration
        $formattedData = $assignments->map(function ($item) {
            return [
                'product_name' => $item->product ? $item->product->name : 'Produit inconnu',
                // On utilise ?? 0 pour éviter le crash si une valeur est nulle en base de données
                'quantity' => $item->quantity ?? $item->quantite_envoyee ?? 0,
                'quantity_received' => $item->quantity_received ?? '-',
                'quantity_returned' => $item->quantity_returned ?? 0,
            ];
        });

        return response()->json(['success' => true, 'data' => $formattedData]);
    }

    //
    public function acceptAssignment(Request $request, $referenceBon) // 🌟 Reçoit la référence désormais
{
    DB::beginTransaction();
    try {
        // 1. Récupérer TOUTES les lignes de ce bon en attente pour cet agent
        $assignments = Assignment::where('reference_bon', $referenceBon)
            ->where('receiver_id', auth()->id())
            ->where('status', 'en_attente')
            ->get();

        if ($assignments->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Aucune affectation en attente trouvée pour ce bon.'], 404);
        }

        $agentSessionActive = WorkSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$agentSessionActive) {
            return response()->json(['status' => false, 'message' => 'Vous devez ouvrir une session de travail avant d\'accepter du stock.'], 400);
        }

        // 2. Boucler sur chaque produit du bon pour mettre à jour et transférer dans le stock réel
        foreach ($assignments as $assignment) {
            $assignment->update([
                'quantity_received' => $assignment->quantity,
                'status' => 'approuve',
                'work_session_id' => $agentSessionActive->id,
                'accepted_at' => now(),
            ]);

            AgentStock::updateOrCreate([
                'user_id' => $assignment->receiver_id,
                'product_id' => $assignment->product_id,
            ], [
                'quantity' => DB::raw("quantity + {$assignment->quantity}"),
                'price' => $request->price ?? 0,
            ]);
        }

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Tout le stock du bon a été accepté avec succès !']);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['status' => false, 'message' => 'Erreur : '.$e->getMessage()], 500);
    }
}
}
