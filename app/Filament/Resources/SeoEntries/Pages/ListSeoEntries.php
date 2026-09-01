<?php

namespace App\Filament\Resources\SeoEntries\Pages;

use App\Filament\Resources\SeoEntries\SeoEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeoEntries extends ListRecords
{
    protected static string $resource = SeoEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni SEO kaydı'),
        ];
    }
}
