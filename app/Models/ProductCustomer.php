<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PayementAlloctaion;
use Illuminate\Notifications\Notifiable;
use App\Models\Sale;

class ProductCustomer extends Model
{
    use HasFactory, Notifiable,SoftDeletes;

    protected $guarded = [];

    public function sales(): hasMany
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

    public function getTotalDebtAttribute()
    {

        return $this->sales()
            ->whereIn('payment_status', ['Unpaid', 'Partial'])
            ->sum('balance');

    }
    public function getLastPaymentAmountAttribute(){
        $LastAllocution = PayementAllocation::whereHas('sale',function($q){
            $q->where('customer_id',$this->id);
            
        })
        ->latest()
         ->first();
         return $LastAllocution ? $LastAllocution->amount_allocated :0;
    }

}
