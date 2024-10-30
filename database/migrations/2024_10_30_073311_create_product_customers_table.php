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
        Schema::create('product_customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->nullable(true);
            $table->string('name', 100);
            $table->string('phone', 50);
            $table->string('email', 100)->nullable(true);
            $table->enum('gender', ['m', 'f'])->default('m');
            $table->string('address', 100)->nullable(true);
            $table->decimal('balance', 10, 2)->nullable(true);
            $table->integer('orders')->nullable(true);
            $table->date('last_order')->nullable(true);
            $table->enum('status', ['on', 'off'])->default('on');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_customers');
    }
};
