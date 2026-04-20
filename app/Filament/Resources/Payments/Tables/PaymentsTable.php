<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('receipt_number')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('loan.loan_number')
                    ->label('Loan #')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('loan.borrower.first_name')
                    ->label('Borrower')
                    ->formatStateUsing(fn ($record) => "{$record->loan->borrower->first_name} {$record->loan->borrower->last_name}")
                    ->searchable(['first_name', 'last_name']),
                \Filament\Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'mobile_money' => 'Mobile Money',
                        'cheque' => 'Cheque',
                        'online' => 'Online',
                    ]),
                \Filament\Tables\Filters\Filter::make('payment_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
