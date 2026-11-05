<?php

namespace App\Filament\Resources\ServicePlanResource\Pages;

use App\Filament\Resources\ServicePlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServicePlan extends CreateRecord
{
    protected static string $resource = ServicePlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
