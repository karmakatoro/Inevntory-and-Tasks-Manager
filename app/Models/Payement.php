<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PayementAllocation;
use App\Models\User;
// C'EST CETTE LIGNE QUI DOIT ÊTRE EXACTEMENT COMME ÇA :
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payement extends Model
{
    use HasFactory;

     protected $fillable = [
        'sale_id',
        'work_session_id',
        'amount',
        'payment_method',
        'recorded_by',   // L'ID de l'agent qui a encaissé
        'payment_date',
        'reference_id',  // Utile pour stocker l'ID de transaction M-Pesa ou Airtel
    ];

    public function allocations()
{
    return $this->hasMany(PayementAllocation::class, 'payment_id');
}
    public function work():belongsTo{
        return $this->belongsTo(WorkSession::class,'work_session_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
