<?php

namespace App\Filament\Resources\ScopeGroups\Pages;

use App\Filament\Resources\ScopeGroups\ScopeGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScopeGroups extends ListRecords
{
    protected static string $resource = ScopeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Yeni Kapsam Grubu')];
    }
}
