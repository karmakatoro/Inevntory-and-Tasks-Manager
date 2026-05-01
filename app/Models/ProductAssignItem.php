<?php

namespace App\Models;
use App\Models\Product;
use App\Models\ProductAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ProductAssignItem extends Model
{
    use HasFactory;
    public function assignment()
    {
        return $this->belongsTo(ProductAssignment ::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
