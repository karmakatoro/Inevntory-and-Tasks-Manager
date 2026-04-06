<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected static function booted(){
        static::creating(function($sale){
            $lastSale = self::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastSale ?(int) substr($lastSale->invoice_number,-4) +1 :1;
            $sale->invoice_number = 'FAC-'.date('Y').'-'.str_pad($number,4,'0',STR_PAD_LEFT);
        });
    }
    public function updatePayementStatus(){
        $this->amount_paid = $this->payements()->sum('amount');
        $this->balance = $this->total_amount - $this->amount_paid;
        if($this->balance <= 0){
            $this->payement_status = 'Paid';
        }
        elseif($this->amount_paid > 0){
            $this->payement_status = 'Partial';
        }
        else{
            $this->payement_status = 'Unpaid';
        }
        $this->save();
    }
}
