<?php

namespace App\Filament\Resources\Loans\Schemas;

use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Loan Origination')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\Select::make('borrower_id')
                            ->relationship('borrower', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name} ({$record->national_id})")
                            ->searchable()
                            ->required(),
                        \Filament\Forms\Components\Select::make('loan_product_id')
                            ->relationship('loanProduct', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                if (!$state) return;
                                $product = \App\Models\LoanProduct::find($state);
                                if ($product) {
                                    $set('interest_rate', $product->interest_rate);
                                    $set('interest_type', $product->interest_type);
                                    $set('tenure_months', $product->max_tenure_months);
                                    $set('repayment_frequency', $product->repayment_frequency);
                                    $set('grace_period_months', $product->grace_period_months);
                                    $set('penalty_type', $product->late_penalty_type);
                                    $set('penalty_amount', $product->late_penalty_value);
                                }
                            }),
                        \Filament\Forms\Components\TextInput::make('loan_number')
                            ->default('LN-' . strtoupper(uniqid()))
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('loan_officer_id')
                            ->relationship('loanOfficer', 'name')
                            ->default(auth()->id())
                            ->searchable(),
                    ]),

                \Filament\Forms\Components\Section::make('Loan Parameters')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('applied_amount')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->live()
                            ->afterStateUpdated(fn ($state, \Filament\Forms\Set $set) => $set('approved_amount', $state)),
                        \Filament\Forms\Components\TextInput::make('approved_amount')
                            ->numeric()
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
                        \Filament\Forms\Components\TextInput::make('tenure_months')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\Select::make('repayment_frequency')
                            ->options([
                                'monthly' => 'Monthly',
                                'weekly' => 'Weekly',
                                'biweekly' => 'Bi-Weekly',
                                'daily' => 'Daily',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('grace_period_months')
                            ->numeric()
                            ->default(0),
                        \Filament\Forms\Components\Select::make('penalty_type')
                            ->options([
                                'fixed' => 'Fixed',
                                'percentage' => 'Percentage',
                            ]),
                        \Filament\Forms\Components\TextInput::make('penalty_amount')
                            ->numeric()
                            ->default(0),
                    ]),

                \Filament\Forms\Components\Section::make('Collateral & Purpose')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('purpose')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('collateral_type')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('collateral_value')
                            ->numeric()
                            ->prefix('$'),
                        \Filament\Forms\Components\Textarea::make('collateral_description')
                            ->columnSpanFull(),
                    ]),

                \Filament\Forms\Components\Section::make('Status & Dates')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'under_review' => 'Under Review',
                                'approved' => 'Approved',
                                'disbursed' => 'Disbursed',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'defaulted' => 'Defaulted',
                                'written_off' => 'Written Off',
                            ])
                            ->default('pending')
                            ->required(),
                        \Filament\Forms\Components\DateTimePicker::make('applied_at')
                            ->default(now()),
                    ]),
            ]);
    }
}
