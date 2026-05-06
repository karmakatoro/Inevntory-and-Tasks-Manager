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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            // Les acteurs
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('product_customers')->onDelete('set null');
            $table->foreignId('work_session_id')->constrained('work_sessions')->cascadeOnDelete();
            $table->float('total_amount', 12, 2)->default(0); // Montant total de la facture
            $table->float('amount_paid', 12, 2)->default(0);   // Somme déjà encaissée
            $table->float('balance', 12, 2)->default(0);       // Reste à payer (Dette)
            // État de la transaction
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('unpaid');
            $table->string('payment_method')->nullable(); // cash, m-pesa, credit
            $table->text('notes')->nullable(); // Pour des précisions sur le crédit
            $table->timestamps();
             $table->decimal('latitude', 10, 8)->nullable();
         $table->decimal('longitude', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
