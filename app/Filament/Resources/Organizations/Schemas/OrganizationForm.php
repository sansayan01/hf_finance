<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Organization Details')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->afterStateUpdated(fn ($state, \Filament\Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        \Filament\Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        \Filament\Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('organization-logos'),
                        \Filament\Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\KeyValue::make('settings')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
