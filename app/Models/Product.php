<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        self::created(function ($product) {
            $length = 6;
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            $uniqueCode = 'SKU-0' . $product->id . '-' . $randomString;
            $product->update(['code' => $uniqueCode]);
            $product->update(['slug' => Str::slug($product->name)]);
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
    public function product_stock()
    {
        return $this->hasMany(ProductStock::class);
    }
}
