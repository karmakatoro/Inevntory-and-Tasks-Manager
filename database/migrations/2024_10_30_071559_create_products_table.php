<?php

use App\Models\User;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->nullable(true)->unique();
            $table->string('slug', 500)->nullable(true)->unique();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignIdFor(ProductCategory::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('subcategories')->nullable(true);
            $table->string('name', 100);
            $table->string('description', 500)->nullable(true);
            $table->string('photo', 50);
            $table->string('gallery', '500')->nullable(true);
            $table->decimal('price', 10, 2)->nullable(true);
            $table->float('quantity', 10, 2)->default(0);
            $table->enum('cmp', ['0', '1'])->default('1');
            $table->enum('status', ['on', 'off']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
