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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('repayment_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, bank_transfer, mobile_money, cheque, online
            $table->string('reference_number')->nullable();
            $table->date('payment_date');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
