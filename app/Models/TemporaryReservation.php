<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User; // N'oublie pas d'importer User

class TemporaryReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'product_id',
        'quantity',
        'expires_at',
        'manager_id',
        'agent_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    // RELATIONS
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function agent() {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function manager() {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // SCOPES (Pour filtrer facilement tes requêtes)
    public function scopeActive(Builder $query) {
        return $query->where('expires_at', '>', now());
    }

    public function scopeExpired(Builder $query) {
        return $query->where('expires_at', '<=', now());
    }

    // LOGIQUE DE NETTOYAGE
    public static function clearExpired() {
        return self::expired()->delete();
    }
}
