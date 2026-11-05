<?php

namespace App\Filament\Resources\ServicePlanResource\Pages;

use App\Filament\Resources\ServicePlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServicePlan extends EditRecord
{
    protected static string $resource = ServicePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('Ko‘rish'),
            Actions\DeleteAction::make(),
        ];
    }
}
