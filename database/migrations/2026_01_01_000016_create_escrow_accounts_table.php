<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escrow_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('account_name');
            $table->string('account_number')->unique();
            $table->string('bank_name');
            $table->string('branch_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('held_amount', 15, 2)->default(0);
            $table->decimal('available_balance', 15, 2)->default(0);
            $table->enum('escrow_type', ['loan_disbursement', 'investor_funds', 'security_deposit', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'frozen'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_accounts');
    }
};
