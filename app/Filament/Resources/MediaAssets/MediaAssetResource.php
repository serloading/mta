<?php

namespace App\Filament\Resources\MediaAssets;

use App\Filament\Resources\MediaAssets\Pages\CreateMediaAsset;
use App\Filament\Resources\MediaAssets\Pages\EditMediaAsset;
use App\Filament\Resources\MediaAssets\Pages\ListMediaAssets;
use App\Models\MediaAsset;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Medya Kütüphanesi';

    protected static ?string $modelLabel = 'Medya';

    protected static ?string $pluralModelLabel = 'Medya kütüphanesi';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dosya')
                ->columns(2)
                ->schema([
                    FileUpload::make('path')
                        ->label('Görsel')
                        ->disk('public')
                        ->directory('media/library')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                        ->maxSize(6144)
                        ->required(),
                    TextInput::make('file_name')->label('Dosya adı')->maxLength(255),
                    TextInput::make('alt_text')->label('Alternatif metin')->maxLength(255)->columnSpanFull(),
                    TextInput::make('mime_type')->label('Dosya tipi')->maxLength(255),
                    TextInput::make('size')->label('Boyut')->numeric(),
                    KeyValue::make('metadata')
                        ->label('Ek veri')
                        ->keyLabel('Alan')
                        ->valueLabel('Değer')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')->label('Görsel')->disk('public')->square(),
                TextColumn::make('file_name')->label('Dosya adı')->searchable()->sortable(),
                TextColumn::make('alt_text')->label('Alternatif metin')->searchable()->limit(50),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaAssets::route('/'),
            'create' => CreateMediaAsset::route('/create'),
            'edit' => EditMediaAsset::route('/{record}/edit'),
        ];
    }
}
