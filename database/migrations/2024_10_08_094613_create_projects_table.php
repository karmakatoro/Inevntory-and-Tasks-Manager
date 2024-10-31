<?php

use App\Models\User;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('title', 100);
            $table->string('logo', 50)->nullable(true);
            $table->string('description', 500);
            $table->longText('details')->nullable(true);
            $table->date('start');
            $table->date('end')->nullable(true);
            $table->longText('files')->nullable(true);
            $table->enum('status', ['pending', 'finished', 'canceled', 'unlaunched']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
