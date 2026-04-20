<?php

namespace App\Filament\Resources\LoanProducts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class LoanProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('interest_rate')
                    ->suffix('%')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('interest_type')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('repayment_frequency')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('interest_type')
                    ->options([
                        'flat' => 'Flat',
                        'declining' => 'Declining Balance',
                        'compound' => 'Compound',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('repayment_frequency')
                    ->options([
                        'monthly' => 'Monthly',
                        'weekly' => 'Weekly',
                        'biweekly' => 'Bi-Weekly',
                        'daily' => 'Daily',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
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
