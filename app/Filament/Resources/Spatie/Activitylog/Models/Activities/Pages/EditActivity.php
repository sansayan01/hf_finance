<?php

namespace App\Filament\Resources\Spatie\Activitylog\Models\Activities\Pages;

use App\Filament\Resources\Spatie\Activitylog\Models\Activities\ActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
