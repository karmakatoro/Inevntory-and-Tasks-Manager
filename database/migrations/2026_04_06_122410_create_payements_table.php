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
        Schema::create('payements', function (Blueprint $table) {
            $table->id();
            // Référence à la vente concernée
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');

            // Montant versé lors de cette transaction précise
            $table->float('amount', 10, 2);

            // Mode de paiement (très important pour ton rapport de caisse)
            $table->enum('payment_method', ['cash', 'm-pesa', 'airtel_money', 'bank', 'credit_note'])
                  ->default('cash');

            // Référence externe (ex: ID transaction M-Pesa ou numéro de bordereau)
            $table->string('reference_number')->nullable();

            // Qui a encaissé l'argent ? (L'agent ou un caissier central)
            $table->foreignId('recorded_by')->constrained('users');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payements');
    }
};
