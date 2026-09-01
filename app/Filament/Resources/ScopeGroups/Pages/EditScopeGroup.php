<?php

namespace App\Filament\Resources\ScopeGroups\Pages;

use App\Filament\Resources\ScopeGroups\ScopeGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScopeGroup extends EditRecord
{
    protected static string $resource = ScopeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin())];
    }
}
