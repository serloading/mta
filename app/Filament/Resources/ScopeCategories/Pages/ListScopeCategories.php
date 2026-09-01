<?php

namespace App\Filament\Resources\ScopeCategories\Pages;

use App\Filament\Resources\ScopeCategories\ScopeCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScopeCategories extends ListRecords
{
    protected static string $resource = ScopeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Yeni Kapsam Kategorisi')];
    }
}
