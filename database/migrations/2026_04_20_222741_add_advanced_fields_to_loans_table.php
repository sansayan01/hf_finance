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
        Schema::table('loans', function (Blueprint $table) {
            $table->integer('grace_period_months')->default(0)->after('tenure_months');
            $table->string('penalty_type')->nullable()->after('interest_type');
            $table->decimal('penalty_amount', 15, 2)->default(0)->after('penalty_type');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['grace_period_months', 'penalty_type', 'penalty_amount']);
        });
    }
};
