<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('Ön yüzde gör')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (): string => route('products.show', $this->record->slug), shouldOpenInNewTab: true),
            Action::make('archive')
                ->label('Arşivle')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update(['status' => 'archived'])),
            DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
