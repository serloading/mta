<?php

namespace App\Filament\Support;

use App\Models\SchemaDefinition;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class SchemaBlockFields
{
    public static function section(?string $area = null): Section
    {
        return Section::make('Schema Yönetimi')
            ->description('Bu kayıtta kullanılacak yapılandırılmış veri tiplerini seçin. Otomatik schema çıktıları korunur; buradaki alanlar ek düzenleme katmanı olarak sayfaya eklenir.')
            ->schema([
                Repeater::make('schema_blocks')
                    ->label('Seçili schemalar')
                    ->addActionLabel('Schema ekle')
                    ->reorderable()
                    ->collapsible()
                    ->defaultItems(0)
                    ->schema([
                        Select::make('type')
                            ->label('Schema türü')
                            ->options(fn () => SchemaDefinition::optionsFor($area))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Schema başlığı')
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        KeyValue::make('payload')
                            ->label('Düzenlenebilir alanlar')
                            ->keyLabel('Alan adı')
                            ->valueLabel('Değer')
                            ->helperText('Örnek: name, description, url, image, telephone. Boş bırakılan alanlar schema tanımındaki varsayılanlardan gelir.')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
