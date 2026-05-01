<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'subcategories' => 'array',
        'gallery' => 'array',
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
        'status',
    ];

    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(function ($product) {
    //         if (auth()->check()) {
    //             $product->user()->associate(auth()->user()->id);
    //         }

    //         // On vérifie si la catégorie est présente dans la requête
    //         if (request()->has('product_category_id')) {
    //             $product->product_category()->associate(request()->product_category_id);
    //         }
    //         $product->slug = str::slug($product->name);
    //     });
    //     self::updating(function ($product) {
    //         if (Route::currentRouteName() == 'products.update') {
    //             $product->user()->associate(auth()->user()->id);
    //             $product->product_category()->associate(request()->product_category_id);
    //             $product->slug = str::slug($product->name);
    //         }
    //     });
    //     self::created(function ($product) {
    //         $length = 6;
    //         $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //         $charactersLength = strlen($characters);
    //         $randomString = '';
    //         for ($i = 0; $i < $length; $i++) {
    //             $randomString .= $characters[rand(0, $charactersLength - 1)];
    //         }
    //         $uniqueCode = 'SKU-0'.$product->id.'-'.$randomString;
    //         $product->update(['code' => $uniqueCode]);

    //     });
    // }
    public static function boot()
    {
        parent::boot();

        self::creating(function ($product) {
            // Utilise l'ID de l'utilisateur connecté s'il existe
            if (auth()->check()) {
                $product->user_id = $product->user_id ?? auth()->id();
            }

            // Utilise la catégorie de la requête seulement si elle existe
            if (request()->filled('product_category_id')) {
                $product->product_category_id = request()->product_category_id;
            }

            $product->slug = Str::slug($product->name);
        });

        self::updating(function ($product) {
            if (Route::currentRouteName() == 'products.update') {
                if (auth()->check()) {
                    $product->user_id = $product->user_id ?? auth()->id();
                }
                $product->slug = Str::slug($product->name);
            }
        });

        self::created(function ($product) {
            $randomString = strtoupper(Str::random(6));
            $uniqueCode = 'SKU-0'.$product->id.'-'.$randomString;

            // Mise à jour silencieuse
            $product->updateQuietly(['code' => $uniqueCode]);
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

    public function reservations()
    {
        return $this->hasMany(TemporaryReservation::class);
    }

    public function getAvailableStockAttribute()
    {
        $reserved = $this->reservations()->active()->sum('quantity');

        return $this->quantity - $reserved;
    }

    public function item()
    {
        return $this->hasMany(SaleItem::class, 'product_id');
    }
}
