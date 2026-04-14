<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Payement;
use App\Models\ProductCustomer;
use App\Models\User;
class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'agent_id',
        'customer_id',
        'total_amount',
        'amount_paid',
        'payment_method',
        'invoice_number', // Si tu génères un numéro de facture
        'status',         // Ex: 'paid', 'debt', 'partial'
        'sale_date',
        'latitude',
        'longitude'
    ];
    protected static function booted(){
        static::creating(function($sale){
            $lastSale = self::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastSale ?(int) substr($lastSale->invoice_number,-4) +1 :1;
            $sale->invoice_number = 'VNT-'.date('Y').'-'.str_pad($number,4,'0',STR_PAD_LEFT);
        });
    }
    public function updatePayementStatus(){
        $this->amount_paid = $this->payements()->sum('amount');
        $this->balance = $this->total_amount - $this->amount_paid;
        if($this->balance <= 0){
            $this->payment_status = 'Paid';
        }
        elseif($this->amount_paid > 0){
            $this->payment_status = 'Partial';
        }
        else{
            $this->payment_status = 'Unpaid';
        }
        $this->save();
    }
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class,'sale_id');
    }

    public function payements(): HasMany
    {
        return $this->hasMany(Payement::class,'sale_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ProductCustomer::class,'customer_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
