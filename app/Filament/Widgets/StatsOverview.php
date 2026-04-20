<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Borrowers', \App\Models\Borrower::count())
                ->description('All registered borrowers')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total Loans', \App\Models\Loan::count())
                ->description('Total loan applications')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Approved Principal', '$' . number_format(\App\Models\Loan::whereIn('status', ['approved', 'disbursed', 'active', 'completed'])->sum('approved_amount'), 2))
                ->description('Total volume approved')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),
        ];
    }
}
