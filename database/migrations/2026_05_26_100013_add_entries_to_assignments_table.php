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
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('work_session_id')->nullable()->constrained('work_sessions')->onDelete('set null')->after('product_id');
            $table->float('quantity_received', 10, 2)->nullable()->after('quantity');
            $table->float('quantity_returned', 10, 2)->default(0)->after('quantity_received');
            $table->enum('status', ['en_attente', 'approuve', 'cloture'])->default('en_attente')->after('quantity_returned');
            $table->timestamp('accepted_at')->nullable()->after('updated_at');
            $table->timestamp('closed_at')->nullable()->after('accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            //
        });
    }
};
