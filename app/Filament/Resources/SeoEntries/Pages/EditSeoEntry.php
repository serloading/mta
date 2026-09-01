<?php

namespace App\Filament\Resources\SeoEntries\Pages;

use App\Filament\Resources\SeoEntries\SeoEntryResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditSeoEntry extends EditRecord
{
    protected static string $resource = SeoEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('Ön yüzde gör')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (): string => url($this->record->path ?: '/'), shouldOpenInNewTab: true),
            DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
