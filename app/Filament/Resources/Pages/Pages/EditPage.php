<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('Ön yüzde gör')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (): string => url('/' . ltrim($this->record->slug, '/')), shouldOpenInNewTab: true),
            Action::make('archive')
                ->label('Arşivle')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update(['status' => 'archived'])),
            DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
