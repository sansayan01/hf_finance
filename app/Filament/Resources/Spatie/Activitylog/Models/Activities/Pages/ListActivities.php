<?php

namespace App\Filament\Resources\Spatie\Activitylog\Models\Activities\Pages;

use App\Filament\Resources\Spatie\Activitylog\Models\Activities\ActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
