<?php

namespace App\Filament\Resources\Spatie\Activitylog\Models\Activities;

use App\Filament\Resources\Spatie\Activitylog\Models\Activities\Pages\CreateActivity;
use App\Filament\Resources\Spatie\Activitylog\Models\Activities\Pages\EditActivity;
use App\Filament\Resources\Spatie\Activitylog\Models\Activities\Pages\ListActivities;
use App\Filament\Resources\Spatie\Activitylog\Models\Activities\Pages\ViewActivity;
use App\Filament\Resources\Spatie\Activitylog\Models\Activities\Schemas\ActivityForm;
use App\Filament\Resources\Spatie\Activitylog\Models\Activities\Schemas\ActivityInfolist;
use App\Filament\Resources\Spatie\Activitylog\Models\Activities\Tables\ActivitiesTable;
use App\Models\Spatie\Activitylog\Models\Activity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'create' => CreateActivity::route('/create'),
            'view' => ViewActivity::route('/{record}'),
            'edit' => EditActivity::route('/{record}/edit'),
        ];
    }
}
