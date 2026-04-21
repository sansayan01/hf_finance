<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('gateway_name');
            $table->enum('gateway_type', ['razorpay', 'stripe', 'paypal', 'payu', 'ccavenue', 'other']);
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->string('merchant_id')->nullable();
            $table->boolean('test_mode')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('config')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
