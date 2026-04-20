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
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('min_amount', 15, 2);
            $table->decimal('max_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->string('interest_type'); // flat, declining, compound
            $table->integer('min_tenure_months');
            $table->integer('max_tenure_months');
            $table->string('repayment_frequency'); // monthly, weekly, biweekly, daily
            $table->string('processing_fee_type'); // fixed, percentage
            $table->decimal('processing_fee_value', 15, 2);
            $table->string('late_penalty_type'); // fixed, percentage
            $table->decimal('late_penalty_value', 15, 2);
            $table->integer('grace_period_days')->default(0);
            $table->boolean('requires_guarantor')->default(false);
            $table->boolean('requires_collateral')->default(false);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_products');
    }
};
