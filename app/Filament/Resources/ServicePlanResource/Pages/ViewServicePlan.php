<?php

namespace App\Filament\Resources\ServicePlanResource\Pages;

use App\Filament\Resources\ServicePlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServicePlan extends ViewRecord
{
    protected static string $resource = ServicePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Tahrirlash')->icon('heroicon-o-pencil-square'),
        ];
    }
}
