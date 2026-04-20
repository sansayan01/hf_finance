<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LatestLoans extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => \App\Models\Loan::query()->latest()->limit(5))
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('loan_number')
                    ->label('Loan #'),
                \Filament\Tables\Columns\TextColumn::make('borrower.first_name')
                    ->label('Borrower')
                    ->formatStateUsing(fn ($record) => "{$record->borrower->first_name} {$record->borrower->last_name}"),
                \Filament\Tables\Columns\TextColumn::make('applied_amount')
                    ->money('USD'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
