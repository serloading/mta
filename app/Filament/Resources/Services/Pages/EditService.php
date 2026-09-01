<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('Ön yüzde gör')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (): string => route('services.show', $this->record->slug), shouldOpenInNewTab: true),
            DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
