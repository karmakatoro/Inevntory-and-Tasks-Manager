<?php

namespace App\Models;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'price',
        'quantity',

    ];
    public function agent(){
        return $this->belongsTo(User::class,'user_id');

    }
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
    public function stocks()
{
    return $this->hasMany(AgentStock::class, 'user_id');
}

/**
 * Récupérer toutes les attributions reçues par cet agent
 */
public function assignmentsReceived()
{
    return $this->hasMany(Assignment::class, 'receiver_id');
}

/**
 * Récupérer toutes les attributions effectuées par cet admin (expéditeur)
 */
public function assignmentsSent()
{
    return $this->hasMany(Assignment::class, 'sender_id');
}
}
