<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'slug',
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

    protected static function boot()
    {
        parent::boot();

        // 🔥 CREATE
        static::creating(function ($product) {

            // ⚡ SAFE USER ID (seed + API)
            if (empty($product->user_id)) {
                $product->user_id = auth()->id() ?? 1;
            }

            // ⚡ SLUG UNIQUE (CRUCIAL pour 15K)
            $product->slug = Str::slug($product->name . '-' . Str::random(8));
        });

        // 🔥 UPDATE
        static::updating(function ($product) {

            $product->slug = Str::slug($product->name . '-' . Str::random(8));
        });

        // 🔥 AUTO CODE SKU
        static::created(function ($product) {

            $product->updateQuietly([
                'code' => 'SKU-' . $product->id . '-' . strtoupper(Str::random(6))
            ]);
        });
    }

    // ================= RELATIONS =================

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

    public function item()
    {
        return $this->hasMany(SaleItem::class, 'product_id');
    }

    // ================= ACCESSOR =================

    public function getAvailableStockAttribute()
    {
        $reserved = $this->reservations()->active()->sum('quantity');

        return $this->quantity - $reserved;
    }
}