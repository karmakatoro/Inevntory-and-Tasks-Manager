<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  \Illuminate\Database\Eloquent\Relations\BelongsTo;
class DailyClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'total_amount',
        'status',
        'cash_received',
    ];
  
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

}
