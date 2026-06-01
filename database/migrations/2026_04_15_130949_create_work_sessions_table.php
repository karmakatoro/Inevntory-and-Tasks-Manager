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
        Schema::create('work_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->enum('status', ['open', 'en_attente_cloture', 'stock_valide', 'closed'])->default('open');
            $table->decimal('total_loss_value', 10, 2)->default(0.00);
            // Pour le contrôle de caisse physique
            $table->decimal('opening_cash', 15, 2)->default(0); 
            $table->decimal('closing_cash', 15, 2)->nullable();
          $table->text('note')->nullable();
          $table->decimal('difference', 15, 2)->default(0);
            $table->timestamps();


       

            // sale_items 
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_sessions');
    }
};
