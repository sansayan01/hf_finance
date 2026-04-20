<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo organization
        $org = \App\Models\Organization::create([
            'name' => 'Demo Finance Ltd',
            'slug' => 'demo-finance',
            'email' => 'admin@demofinance.com',
            'phone' => '1234567890',
            'address' => '123 Finance Street, Money City',
        ]);

        // Create Super Admin (no organization)
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'super@hffinance.com',
            'password' => bcrypt('password'), // password
        ]);
        $superAdmin->assignRole('super_admin');

        // Create Org Admin
        $orgAdmin = \App\Models\User::create([
            'organization_id' => $org->id,
            'name' => 'Org Admin',
            'email' => 'admin@demofinance.com',
            'password' => bcrypt('password'),
        ]);
        $orgAdmin->assignRole('org_admin');

        // Create Loan Officer
        $loanOfficer = \App\Models\User::create([
            'organization_id' => $org->id,
            'name' => 'John Loan Officer',
            'email' => 'john@demofinance.com',
            'password' => bcrypt('password'),
        ]);
        $loanOfficer->assignRole('loan_officer');

        // Create Demo Borrowers
        $borrower1 = \App\Models\Borrower::create([
            'organization_id' => $org->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '9876543210',
            'national_id' => 'NID-001',
            'kyc_status' => 'verified',
            'status' => 'active',
        ]);

        $borrower2 = \App\Models\Borrower::create([
            'organization_id' => $org->id,
            'first_name' => 'Bob',
            'last_name' => 'Smith',
            'email' => 'bob@example.com',
            'phone' => '9876543211',
            'national_id' => 'NID-002',
            'kyc_status' => 'pending',
            'status' => 'active',
        ]);

        // Create Demo Loan Products
        \App\Models\LoanProduct::create([
            'organization_id' => $org->id,
            'name' => 'Personal Loan (Flat)',
            'code' => 'PL-FLAT',
            'description' => 'Personal loan with flat interest rate.',
            'min_amount' => 1000,
            'max_amount' => 50000,
            'interest_rate' => 12.00,
            'interest_type' => 'flat',
            'min_tenure_months' => 6,
            'max_tenure_months' => 36,
            'repayment_frequency' => 'monthly',
            'processing_fee_type' => 'fixed',
            'processing_fee_value' => 500,
            'late_penalty_type' => 'percentage',
            'late_penalty_value' => 2.00,
            'grace_period_days' => 5,
            'status' => 'active',
        ]);

        \App\Models\LoanProduct::create([
            'organization_id' => $org->id,
            'name' => 'Business Loan (Declining)',
            'code' => 'BL-DECLINE',
            'description' => 'Business loan with declining balance interest rate.',
            'min_amount' => 50000,
            'max_amount' => 500000,
            'interest_rate' => 18.00,
            'interest_type' => 'declining',
            'min_tenure_months' => 12,
            'max_tenure_months' => 60,
            'repayment_frequency' => 'monthly',
            'processing_fee_type' => 'percentage',
            'processing_fee_value' => 1.50,
            'late_penalty_type' => 'fixed',
            'late_penalty_value' => 1000,
            'grace_period_days' => 7,
            'status' => 'active',
        ]);
    }
}
