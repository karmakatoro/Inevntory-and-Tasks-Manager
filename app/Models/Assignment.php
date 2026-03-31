<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'product_id',
        'quantity'
    ];
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * L'agent de terrain qui a reçu le lot
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Le produit qui a été transféré
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function agentStocks()
{
    return $this->hasMany(AgentStock::class);
}

/**
 * Voir tout l'historique des attributions pour ce produit
 */
public function assignments()
{
    return $this->hasMany(Assignment::class);
}
}
