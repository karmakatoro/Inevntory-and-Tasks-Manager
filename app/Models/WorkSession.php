<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Payement;
use App\Models\Sale;
use App\Models\User;
use App\Models\CashMovement;

class WorkSession extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function workMovement():HasMany{
        return $this->HasMany(CashMovement::class,'work_session_id');
    }
    
    public function work(): HasMany
    {
        return $this->hasMany(Sale::class, 'work_session_id');
    }
    
    public function workpaie(): HasMany
    {
        return $this->hasMany(Payement::class, 'work_session_id');
    }
     public function user():belongsTo
     {
        return $this->belongsTo(User::class,'user_id');
     }
}

