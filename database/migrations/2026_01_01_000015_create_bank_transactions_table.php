<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained();
            $table->date('transaction_date');
            $table->string('reference_number');
            $table->string('description')->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('counterparty_name')->nullable();
            $table->string('counterparty_account')->nullable();
            $table->enum('status', ['unreconciled', 'reconciled', 'ignored'])->default('unreconciled');
            $table->foreignId('reconciled_payment_id')->nullable()->constrained('payments');
            $table->foreignId('reconciled_by')->nullable()->constrained('users');
            $table->timestamp('reconciled_at')->nullable();
            $table->string('statement_file')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'transaction_date']);
            $table->index(['organization_id', 'status']);
            $table->unique(['organization_id', 'reference_number', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
