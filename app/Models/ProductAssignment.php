<?php

namespace App\Models;

use App\Models\User ;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAssignment extends Model
{
    use HasFactory;
    protected $guard = [];
    public function isAgent(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function isAdmin(){
        return $this->belongsTo(User::class,'assign_by');
    }
}
