<?php

namespace App\Filament\Resources\ScopeCategories\Pages;

use App\Filament\Resources\ScopeCategories\ScopeCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScopeCategory extends EditRecord
{
    protected static string $resource = ScopeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin())];
    }
}
