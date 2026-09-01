<?php

namespace App\Filament\Resources\SchemaDefinitions\Pages;

use App\Filament\Resources\SchemaDefinitions\SchemaDefinitionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchemaDefinition extends EditRecord
{
    protected static string $resource = SchemaDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
