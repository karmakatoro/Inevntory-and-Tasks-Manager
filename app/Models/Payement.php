<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payement extends Model
{
    use HasFactory;
     protected static function booted(){
        static::created(function($payement){
            $payement->sale->updatePayementStatus();
        });
     }
}
