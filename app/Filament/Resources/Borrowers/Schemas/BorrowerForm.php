<?php

namespace App\Filament\Resources\Borrowers\Schemas;

use Filament\Schemas\Schema;

class BorrowerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Personal Information')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ]),
                        \Filament\Forms\Components\DatePicker::make('date_of_birth')
                            ->native(false),
                        \Filament\Forms\Components\TextInput::make('national_id')
                            ->label('National ID / Passport Number')
                            ->maxLength(255),
                    ]),

                \Filament\Forms\Components\Section::make('Address & Employment')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('state')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('country')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('occupation')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('employer')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('monthly_income')
                            ->numeric()
                            ->prefix('$'),
                        \Filament\Forms\Components\TextInput::make('credit_score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000),
                    ]),

                \Filament\Forms\Components\Section::make('KYC & Status')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\Select::make('kyc_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'blacklisted' => 'Blacklisted',
                            ])
                            ->default('active')
                            ->required(),
                        \Filament\Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('borrower-photos'),
                        \Filament\Forms\Components\FileUpload::make('kyc_documents')
                            ->multiple()
                            ->directory('borrower-kyc'),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
