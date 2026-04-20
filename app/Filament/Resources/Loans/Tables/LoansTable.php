<?php

namespace App\Filament\Resources\Loans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('loan_number')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('borrower.first_name')
                    ->label('Borrower')
                    ->formatStateUsing(fn ($record) => "{$record->borrower->first_name} {$record->borrower->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('loanProduct.name')
                    ->label('Product')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('applied_amount')
                    ->money('USD')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'under_review' => 'info',
                        'approved' => 'success',
                        'disbursed' => 'primary',
                        'active' => 'success',
                        'completed' => 'success',
                        'defaulted' => 'danger',
                        'written_off' => 'danger',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('applied_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'under_review' => 'Under Review',
                        'approved' => 'Approved',
                        'disbursed' => 'Disbursed',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'defaulted' => 'Defaulted',
                        'written_off' => 'Written Off',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('loan_product_id')
                    ->label('Product')
                    ->relationship('loanProduct', 'name'),
            ])
            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    \Filament\Tables\Actions\EditAction::make(),
                    \Filament\Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review']))
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'approved',
                                'approved_at' => now(),
                                'approved_by' => auth()->id(),
                            ]);
                            \Filament\Notifications\Notification::make()
                                ->title('Loan Approved')
                                ->success()
                                ->send();
                        }),
                    \Filament\Tables\Actions\Action::make('disburse')
                        ->label('Disburse')
                        ->icon('heroicon-o-banknotes')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status === 'approved')
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'disbursed',
                                'disbursed_at' => now(),
                                'disbursed_by' => auth()->id(),
                            ]);
                            
                            // Generate repayment schedule
                            app(\App\Services\LoanService::class)->generateRepaymentSchedule($record);

                            \Filament\Notifications\Notification::make()
                                ->title('Loan Disbursed')
                                ->body('Repayment schedule has been generated.')
                                ->success()
                                ->send();
                        }),
                    \Filament\Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
