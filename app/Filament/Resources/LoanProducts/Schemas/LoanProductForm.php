<?php

namespace App\Filament\Resources\LoanProducts\Schemas;

use Filament\Schemas\Schema;

class LoanProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),

                \Filament\Forms\Components\Section::make('Loan Parameters')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('min_amount')
                            ->numeric()
                            ->required()
                            ->prefix('$'),
                        \Filament\Forms\Components\TextInput::make('max_amount')
                            ->numeric()
                            ->required()
                            ->prefix('$'),
                        \Filament\Forms\Components\TextInput::make('interest_rate')
                            ->numeric()
                            ->required()
                            ->suffix('%'),
                        \Filament\Forms\Components\Select::make('interest_type')
                            ->options([
                                'flat' => 'Flat',
                                'declining' => 'Declining Balance',
                                'compound' => 'Compound',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('min_tenure_months')
                            ->numeric()
                            ->required()
                            ->label('Min Tenure (Months)'),
                        \Filament\Forms\Components\TextInput::make('max_tenure_months')
                            ->numeric()
                            ->required()
                            ->label('Max Tenure (Months)'),
                        \Filament\Forms\Components\Select::make('repayment_frequency')
                            ->options([
                                'monthly' => 'Monthly',
                                'weekly' => 'Weekly',
                                'biweekly' => 'Bi-Weekly',
                                'daily' => 'Daily',
                            ])
                            ->required(),
                    ]),

                \Filament\Forms\Components\Section::make('Fees & Penalties')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\Select::make('processing_fee_type')
                            ->options([
                                'fixed' => 'Fixed Amount',
                                'percentage' => 'Percentage of Principal',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('processing_fee_value')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\Select::make('late_penalty_type')
                            ->options([
                                'fixed' => 'Fixed Amount',
                                'percentage' => 'Percentage of Installment',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('late_penalty_value')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('grace_period_days')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),

                \Filament\Forms\Components\Section::make('Requirements & Status')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('requires_guarantor')
                            ->default(false),
                        \Filament\Forms\Components\Toggle::make('requires_collateral')
                            ->default(false),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }
}
