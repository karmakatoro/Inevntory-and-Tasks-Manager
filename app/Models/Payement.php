<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\User;
// C'EST CETTE LIGNE QUI DOIT ÊTRE EXACTEMENT COMME ÇA :
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payement extends Model
{
    use HasFactory;
     protected static function booted(){
        static::created(function($payement){
            $payement->sale->updatePayementStatus();
        });
     }
     protected $fillable = [
        'sale_id',
        'amount',
        'payment_method',
        'recorded_by',   // L'ID de l'agent qui a encaissé
        'payment_date',
        'reference_id',  // Utile pour stocker l'ID de transaction M-Pesa ou Airtel
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class,'sale_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
