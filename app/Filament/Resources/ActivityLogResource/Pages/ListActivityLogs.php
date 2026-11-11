<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\ManageRecords;

class ListActivityLogs extends ManageRecords
{
    protected static string $resource = ActivityLogResource::class;
}
