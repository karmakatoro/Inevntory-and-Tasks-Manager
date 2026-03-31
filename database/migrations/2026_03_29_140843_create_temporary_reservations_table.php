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
    Schema::create('temporary_reservations', function (Blueprint $table) {
        $table->id();

        // L'identifiant de la session du navigateur (pour savoir à qui appartient le panier)
        $table->string('session_id')->index();

        // Relation avec la table products
        $table->foreignId('product_id')
              ->constrained()
              ->onDelete('cascade');
        $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('cascade');
        $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('cascade');
        // La quantité réservée
        $table->integer('quantity')->default(1);

        // Le moment où la réservation expire (ex: dans 10 minutes)
        // TRÈS IMPORTANT : Utilise 'expires_at' (avec un 's') pour être cohérent avec ton code
        $table->timestamp('expires_at');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_reservations');
    }
};
