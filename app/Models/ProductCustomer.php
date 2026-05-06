<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ProductCustomer extends Model
{

    use HasFactory, SoftDeletes,Notifiable;
    protected $guarded = [];
    public function sale():hasMany {
        return $this->hasMany(Sale::class,'customer_id');
    }
}
