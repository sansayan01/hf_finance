<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_officer_id')->nullable()->constrained('users');
            $table->foreignId('parent_loan_id')->nullable()->constrained('loans');
            $table->string('loan_number')->unique();
            $table->decimal('applied_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->decimal('disbursed_amount', 15, 2)->nullable();
            $table->decimal('interest_rate', 8, 4);
            $table->enum('interest_type', ['flat', 'declining', 'compound']);
            $table->integer('tenure_months');
            $table->enum('repayment_frequency', ['daily', 'weekly', 'biweekly', 'monthly']);
            $table->decimal('processing_fee', 15, 2)->default(0);
            $table->decimal('total_interest', 15, 2)->nullable();
            $table->decimal('total_payable', 15, 2)->nullable();
            $table->decimal('principal_outstanding', 15, 2)->default(0);
            $table->decimal('interest_outstanding', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->text('purpose')->nullable();
            $table->string('collateral_type')->nullable();
            $table->decimal('collateral_value', 15, 2)->nullable();
            $table->text('collateral_description')->nullable();
            $table->enum('status', [
                'pending', 'under_review', 'approved', 'rejected',
                'disbursed', 'active', 'completed', 'defaulted', 'written_off',
                'restructured', 'moratorium'
            ])->default('pending');
            $table->enum('npa_status', ['standard', 'substandard', 'doubtful', 'loss'])->nullable();
            $table->integer('days_overdue')->default(0);
            $table->enum('currency', ['INR', 'USD', 'EUR', 'GBP'])->default('INR');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->foreignId('disbursed_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_restructured')->default(false);
            $table->boolean('is_topup')->default(false);
            $table->boolean('moratorium_active')->default(false);
            $table->date('moratorium_start_date')->nullable();
            $table->date('moratorium_end_date')->nullable();
            $table->json('ai_approval_prediction')->nullable();
            $table->decimal('ai_collection_priority', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'borrower_id']);
            $table->index(['organization_id', 'npa_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
