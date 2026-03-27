<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $casts = [
        'subcategories'=>'array',
        'gallery'=>'array'
    ];
    protected $fillable = [
        'code',
        'subcategories',
        'product_category_id',
        'user_id',
        'name',
        'description',
        'photo',
        'gallery',
        'price',
        'quantity',
        'cmp',
        'status'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($product) {
            $product->user()->associate(auth()->user()->id);
            $product->product_category()->associate(request()->product_category_id);
            $product->slug = str::slug($product->name);
        });
        self::updating(function ($product) {
            if (Route::currentRouteName() == 'products.update') {
                $product->user()->associate(auth()->user()->id);
                $product->product_category()->associate(request()->product_category_id);
                $product->slug = str::slug($product->name);
            }
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
            $product->update(['code'=>$uniqueCode]);

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
