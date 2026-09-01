<?php

namespace App\Filament\Resources\TechnicalServices\Pages;

use App\Filament\Resources\TechnicalServices\TechnicalServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTechnicalServices extends ListRecords
{
    protected static string $resource = TechnicalServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Yeni teknik servis')];
    }
}
