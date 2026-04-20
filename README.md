<p align="center">
  <img src="https://ui-avatars.com/api/?name=HF&color=7F9CF5&background=EBF4FF&size=150&rounded=true" alt="HF Finance Logo">
</p>

# 🏦 HF Finance - Next-Gen Loan Management SaaS

An enterprise-grade, multi-tenant Loan Management Software (LMS) built with **Laravel 11** and **Filament v3**. 

HF Finance is designed to allow multiple financial organizations (Microfinance, Cooperatives, Private Lenders) to manage their entire loan lifecycle securely in a single instance with complete data isolation.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20.svg?style=flat&logo=laravel)
![Filament](https://img.shields.io/badge/Filament-3.x-FBBF24.svg?style=flat&logo=filament)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4.svg?style=flat&logo=php)

---

## ✨ Current Architecture & Features

### 🛡️ Enterprise Foundation
*   **Total Multi-Tenancy**: Single-database multi-tenancy using Eloquent Global Scopes. Every record automatically maps to the user's `organization_id`.
*   **Role-Based Access Control (RBAC)**: Powered by Spatie Permission. Distinct roles including `Super Admin`, `Org Admin`, `Loan Officer`, and `Accountant`.
*   **Full Audit Trails**: Every creation, update, and deletion in the core models (Borrowers, Loans, Products, Payments) is tracked with timestamp and user ID using Spatie Activitylog.

### 💰 Core Financial Engine
*   **Dynamic Loan Products**: Define `Min/Max Amount`, `Tenure Limits`, `Processing Fees`, and `Late Penalties` (Fixed or Percentage).
*   **Dual Amortization Algorithms**:
    *   **Flat Interest**: Simple interest calculated upfront and evenly divided.
    *   **Declining Balance**: Complex amortization ensuring borrowers pay less interest as principal decreases.
*   **Intelligent Grace Periods**: Allow "Interest-Only" months where principal repayment is delayed to help borrowers align cash flows.

### ⚡ Operational Automation
*   **Automated Scheduling**: A strict `LoanService` automatically generates 1-to-N repayment schedules upon Loan Disbursement.
*   **FIFO Payment Allocation**: Payments are automatically applied to the oldest pending installments, clearing interest before principal.
*   **Automated Penalty Cron**: A scheduled `PenaltyService` (`php artisan loans:apply-penalties`) calculates grace days and applies late fees securely.

---

## 🚀 Advanced Roadmap (Scaffolded & In-Progress)

We are actively building the foundation to integrate massive Fintech APIs and Web3 logic into the platform:

1.  **🤝 P2P & Syndicated Lending**: (Scaffolded: `Investors` and `Investments` tables). Allowing external capital to fund fractional shares of a loan.
2.  **💼 Broker Commission Engine**: (Scaffolded: `Brokers` and `Commissions` tables). Tracking third-party loan originators and auto-calculating their percentage payouts.
3.  **🎮 Borrower Gamification**: (Scaffolded: `trust_points`). A loyalty system where borrowers earn points for early payments to unlock lower future interest rates.
4.  **🏦 Cloud Accounting Sync**: Upcoming 2-way sync with Xero & QuickBooks for real-time journal entries.
5.  **💬 Conversational AI Collections**: Upcoming integrations with WhatsApp/Twilio for self-service balances and payment links.

---

## 🛠️ Local Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/sansayan01/hf_finance.git
   cd hf_finance
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Update `.env` to use your preferred database (MySQL/SQLite).*

4. **Migrate & Seed the Database**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *The seeder will create the core Roles, a Demo Organization, and dummy Borrowers/Loans.*

5. **Start the Application**
   ```bash
   php artisan serve
   ```

## 🔑 Demo Credentials

Once the server is running, navigate to `http://127.0.0.1:8000/admin`.

*   **Super Admin** (Full view across all Orgs)
    *   **Email**: `super@hffinance.com`
    *   **Password**: `password`
*   **Demo Organization Admin** (Restricted to their Org)
    *   **Email**: `admin@demofinance.com`
    *   **Password**: `password`

---

## 📄 License

The HF Finance application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
