<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    // 1. AJOUT de 'reference_bon' ici pour autoriser sa création automatique
    protected $fillable = [
        'reference_bon', 
        'sender_id',
        'receiver_id',
        'product_id',
        'quantity',
        'work_session_id',
        'quantity_received', 
        'quantity_returned', 
        'status'
    ];

    /**
     * Le gestionnaire ou admin qui a envoyé le lot
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * L'agent de terrain qui a reçu le lot
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * La session de travail associée à cet assignement
     */
    public function workSession()
    {
        return $this->belongsTo(WorkSession::class, 'work_session_id');
    }

    /**
     * Le produit qui a été transféré
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * ÉVÉNEMENT : Génération automatique et sécurisée du Bon de Sortie annuel
     */
    protected static function booted()
    {
        static::creating(function ($assignment) {
            // On ne génère le numéro séquentiel QUE si le Job ne l'a pas déjà fourni
            if (empty($assignment->reference_bon)) {
                
                // On récupère le tout dernier enregistrement de l'année en cours
                $lastAssignment = self::whereYear('created_at', date('Y'))
                    ->latest('id')
                    ->first();

                // Extraction et incrémentation sécurisée
                $number = $lastAssignment ? (int) substr($lastAssignment->reference_bon, -4) + 1 : 1;
                
                $assignment->reference_bon = 'BDS-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}