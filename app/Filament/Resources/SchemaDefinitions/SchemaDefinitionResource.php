<?php

namespace App\Filament\Resources\SchemaDefinitions;

use App\Filament\Resources\SchemaDefinitions\Pages\CreateSchemaDefinition;
use App\Filament\Resources\SchemaDefinitions\Pages\EditSchemaDefinition;
use App\Filament\Resources\SchemaDefinitions\Pages\ListSchemaDefinitions;
use App\Models\SchemaDefinition;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class SchemaDefinitionResource extends Resource
{
    protected static ?string $model = SchemaDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    protected static ?string $navigationLabel = 'Schema Yönetimi';

    protected static ?string $modelLabel = 'Schema tanımı';

    protected static ?string $pluralModelLabel = 'Schema tanımları';

    protected static string|UnitEnum|null $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Schema Tanımı')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Görünen ad')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('key', Str::slug((string) $state, '_'))),
                    TextInput::make('key')
                        ->label('Sistem anahtarı')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('schema_type')
                        ->label('Schema tipi')
                        ->placeholder('Product, Article, FAQPage')
                        ->required()
                        ->maxLength(255),
                    Select::make('applicable_to')
                        ->label('Kullanılacağı alanlar')
                        ->multiple()
                        ->options([
                            'global' => 'Genel',
                            'page' => 'Sayfa',
                            'product' => 'Ürün',
                            'article' => 'Blog içeriği',
                            'service' => 'Hizmet',
                            'technical_service' => 'Teknik servis',
                        ])
                        ->preload(),
                    Textarea::make('description')
                        ->label('Açıklama')
                        ->rows(3)
                        ->columnSpanFull(),
                    KeyValue::make('default_payload')
                        ->label('Varsayılan alanlar')
                        ->keyLabel('Alan adı')
                        ->valueLabel('Varsayılan değer')
                        ->columnSpanFull(),
                    TextInput::make('sort_order')
                        ->label('Sıra')
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Schema')->searchable()->sortable(),
                TextColumn::make('schema_type')->label('Tip')->badge()->searchable(),
                TextColumn::make('applicable_to')->label('Alanlar')->badge()->separator(',')->toggleable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Aktiflik'),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchemaDefinitions::route('/'),
            'create' => CreateSchemaDefinition::route('/create'),
            'edit' => EditSchemaDefinition::route('/{record}/edit'),
        ];
    }
}
