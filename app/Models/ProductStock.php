<?php

namespace App\Models;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
  
    public static function boot()
    {
        parent::boot();
        self::creating(function ($productStock) {
            $productStock->user()->associate(auth()->user()->id);
            $productStock->product()->associate(request()->product_id);
        });
        self::updating(function ($productStock) {
            $productStock->user()->associate(auth()->user()->id);
            $productStock->product()->associate(request()->product_id);
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
