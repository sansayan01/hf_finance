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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('loan_number')->unique();
            $table->decimal('applied_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->decimal('disbursed_amount', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2);
            $table->string('interest_type');
            $table->integer('tenure_months');
            $table->string('repayment_frequency');
            $table->decimal('processing_fee', 15, 2)->default(0);
            $table->decimal('total_interest', 15, 2)->default(0);
            $table->decimal('total_payable', 15, 2)->default(0);
            $table->string('purpose')->nullable();
            $table->string('collateral_type')->nullable();
            $table->decimal('collateral_value', 15, 2)->nullable();
            $table->text('collateral_description')->nullable();
            $table->string('status')->default('pending'); // pending, under_review, approved, disbursed, active, completed, defaulted, written_off
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('disbursed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
