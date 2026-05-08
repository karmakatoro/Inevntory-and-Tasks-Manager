<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\WorkSession;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_session_id',
        'user_id',
        'type',
        'amount',
        'category',
        'description',

    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function work(): belongsTo
    {
        return $this->belongsTo(WorkSession::class, 'work_session_id');
    }
}
