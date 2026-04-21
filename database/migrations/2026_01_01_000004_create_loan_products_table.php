<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('min_amount', 15, 2);
            $table->decimal('max_amount', 15, 2);
            $table->decimal('base_interest_rate', 8, 4);
            $table->decimal('max_interest_rate', 8, 4)->nullable();
            $table->enum('interest_type', ['flat', 'declining', 'compound']);
            $table->integer('min_tenure_months');
            $table->integer('max_tenure_months');
            $table->enum('repayment_frequency', ['daily', 'weekly', 'biweekly', 'monthly']);
            $table->enum('processing_fee_type', ['fixed', 'percentage']);
            $table->decimal('processing_fee_value', 8, 4);
            $table->enum('late_penalty_type', ['fixed', 'percentage']);
            $table->decimal('late_penalty_value', 8, 4);
            $table->integer('grace_period_days')->default(0);
            $table->boolean('requires_guarantor')->default(false);
            $table->boolean('requires_collateral')->default(false);
            $table->boolean('supports_topup')->default(false);
            $table->boolean('supports_prepayment')->default(true);
            $table->decimal('prepayment_penalty_rate', 5, 2)->default(0);
            $table->boolean('dynamic_pricing_enabled')->default(false);
            $table->json('risk_tier_rates')->nullable();
            $table->boolean('multi_currency_enabled')->default(false);
            $table->json('allowed_currencies')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_products');
    }
};
