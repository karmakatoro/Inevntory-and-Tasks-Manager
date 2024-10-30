<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($product) {
            $product->user()->associate(auth()->user()->id);
            $product->product_category()->associate(request()->product_category_id);
        });
        self::updating(function ($product) {
            $product->user()->associate(auth()->user()->id);
            $product->product_category()->associate(request()->product_category_id);
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
