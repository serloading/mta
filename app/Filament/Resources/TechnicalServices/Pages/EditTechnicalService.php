<?php

namespace App\Filament\Resources\TechnicalServices\Pages;

use App\Filament\Resources\TechnicalServices\TechnicalServiceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditTechnicalService extends EditRecord
{
    protected static string $resource = TechnicalServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('Ön yüzde gör')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (): string => route('technical-services.show', $this->record->slug), shouldOpenInNewTab: true),
            DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
