<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use HasFactory, SoftDeletes;

    // C'est cette liste qui autorise l'insertion
    protected $fillable = [
        'mouvement',
        'product_id',
        'quantity',
        'status',
        'user_id',
        'price',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($productStock) {
            // Sécurité User : On n'associe via l'auth que si on est sur le Web
            if (auth()->check()) {
                $productStock->user_id = $productStock->user_id ?? auth()->id();
            }

            // Sécurité Produit : On n'associe via la requête que si product_id est présent
            if (request()->has('product_id')) {
                $productStock->product_id = $productStock->product_id ?? request()->product_id;
            }
        });

        self::updating(function ($productStock) {
            // On ne met à jour l'utilisateur que si c'est une action Web délibérée
            if (auth()->check()) {
                $productStock->user_id = auth()->id();
            }
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
