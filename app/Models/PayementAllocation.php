<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Payement;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <--- TRÈS IMPORTANT

class PayementAllocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_id',
        'sale_id',
        'user_id',
        'amount_allocated',
        'note'
    ];
    

protected static function booted()
{
    static::created(function ($allocation) {
        // On récupère la vente fraîchement depuis la DB pour être sûr
        $sale = $allocation->sale()->first(); 
        if ($sale) {
            $sale->updatePayementStatus();
        }
    });
}
      public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class,'sale_id');
    }
      public function payement(): BelongsTo
    {
        return $this->belongsTo(Payement::class,'payment_id');
    }
}
