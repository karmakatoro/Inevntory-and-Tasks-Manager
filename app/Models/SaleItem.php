<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payement;
use App\Models\ProductCustomer;
use App\Models\User;
use App\Models\Sale;

class SaleItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class,'sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
