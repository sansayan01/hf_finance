<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cibil_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            $table->string('bureau_name');
            $table->string('report_number');
            $table->date('report_date');
            $table->integer('credit_score');
            $table->enum('score_category', ['excellent', 'good', 'fair', 'poor', 'bad']);
            $table->integer('total_accounts');
            $table->integer('active_accounts');
            $table->integer('closed_accounts')->default(0);
            $table->integer('overdue_accounts')->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('total_credit_limit', 15, 2)->default(0);
            $table->integer('enquiries_last_6_months')->default(0);
            $table->integer('enquiries_last_12_months')->default(0);
            $table->json('accounts_data')->nullable();
            $table->json('enquiries_data')->nullable();
            $table->text('report_pdf_path')->nullable();
            $table->enum('status', ['requested', 'received', 'failed'])->default('requested');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['borrower_id', 'bureau_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cibil_reports');
    }
};
