<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Payment Details')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\Select::make('loan_id')
                            ->relationship('loan', 'loan_number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->loan_number} - {$record->borrower->first_name} {$record->borrower->last_name}")
                            ->searchable()
                            ->required()
                            ->live(),
                        \Filament\Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix('$'),
                        \Filament\Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'mobile_money' => 'Mobile Money',
                                'cheque' => 'Cheque',
                                'online' => 'Online',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('reference_number')
                            ->maxLength(255),
                        \Filament\Forms\Components\DatePicker::make('payment_date')
                            ->default(now())
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('receipt_number')
                            ->default('RCP-' . strtoupper(uniqid()))
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
