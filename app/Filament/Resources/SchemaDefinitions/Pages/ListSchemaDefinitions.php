<?php

namespace App\Filament\Resources\SchemaDefinitions\Pages;

use App\Filament\Resources\SchemaDefinitions\SchemaDefinitionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchemaDefinitions extends ListRecords
{
    protected static string $resource = SchemaDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni schema'),
        ];
    }
}
