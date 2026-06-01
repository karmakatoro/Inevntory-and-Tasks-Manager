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
        Schema::create('assignments', function (Blueprint $table) {

            $table->id();

            // Référence du bon d'affectation
            $table->string('reference_bon', 30)->nullable();

            // Utilisateur qui affecte le produit
            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Utilisateur qui reçoit le produit
            $table->foreignId('receiver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Produit concerné
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Session de travail associée
            $table->foreignId('work_session_id')
                ->nullable()
                ->constrained('work_sessions')
                ->nullOnDelete();

            // Quantité affectée
            $table->decimal('quantity', 10, 2);

            // Quantité réellement reçue
            $table->decimal('quantity_received', 10, 2)
                ->nullable();

            // Quantité retournée
            $table->decimal('quantity_returned', 10, 2)
                ->default(0);

            // Statut
            $table->enum('status', [
                'en_attente',
                'approuve',
                'cloture'
            ])->default('en_attente');

            // Contrôle physique du stock
            $table->integer('physical_quantity')
                ->nullable();

            // Écart constaté
            $table->integer('stock_discrepancy')
                ->default(0);

            // Valeur de la perte
            $table->decimal('loss_value', 10, 2)
                ->default(0);

            // Dates métier
            $table->timestamp('accepted_at')
                ->nullable();

            $table->timestamp('closed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};