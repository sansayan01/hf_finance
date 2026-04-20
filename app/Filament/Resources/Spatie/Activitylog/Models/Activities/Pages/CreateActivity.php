<?php

namespace App\Filament\Resources\Spatie\Activitylog\Models\Activities\Pages;

use App\Filament\Resources\Spatie\Activitylog\Models\Activities\ActivityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;
}
