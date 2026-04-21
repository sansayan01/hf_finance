<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('repayment_schedule_id')->nullable()->constrained();
            $table->foreignId('borrower_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_portion', 15, 2)->default(0);
            $table->decimal('interest_portion', 15, 2)->default(0);
            $table->decimal('penalty_portion', 15, 2)->default(0);
            $table->enum('payment_type', ['emi', 'prepayment', 'partial', 'penalty', 'charge', 'other']);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mobile_money', 'cheque', 'online', 'auto_debit', 'upi', 'card']);
            $table->string('reference_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('payment_date');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->string('receipt_number')->unique()->nullable();
            $table->string('receipt_pdf')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('pending');
            $table->boolean('reconciled')->default(false);
            $table->timestamp('reconciled_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'payment_date']);
            $table->index(['organization_id', 'reconciled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
