<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payement_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payements')->onDelete('cascade');
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->decimal('amount_allocated', 10, 2);
            $table->foreignId('user_id')->constrained('users');
             $table->string('note')->nullable();
            $table->index(['payment_id', 'sale_id']); 
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payement_allocations');
    }
};
