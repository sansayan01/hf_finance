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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_invested', 15, 2);
            $table->decimal('expected_return', 15, 2)->default(0);
            $table->string('status')->default('active'); // active, completed, defaulted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
