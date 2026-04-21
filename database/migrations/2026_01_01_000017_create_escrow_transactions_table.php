<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escrow_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->morphs('transactionable');
            $table->enum('transaction_type', ['credit', 'debit', 'hold', 'release', 'transfer']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('processed_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['escrow_account_id', 'transaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_transactions');
    }
};
