<?php

namespace App\Filament\Resources\SeoEntries;

use App\Filament\Resources\SeoEntries\Pages\CreateSeoEntry;
use App\Filament\Resources\SeoEntries\Pages\EditSeoEntry;
use App\Filament\Resources\SeoEntries\Pages\ListSeoEntries;
use App\Filament\Support\SchemaBlockFields;
use App\Models\SeoEntry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class SeoEntryResource extends Resource
{
    protected static ?string $model = SeoEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'Sayfa SEO Yönetimi';

    protected static ?string $modelLabel = 'Sayfa SEO kaydı';

    protected static ?string $pluralModelLabel = 'Sayfa SEO kayıtları';

    protected static string|UnitEnum|null $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sayfa')
                ->columns(2)
                ->schema([
                    TextInput::make('path')
                        ->label('Sayfa yolu')
                        ->placeholder('/urunler')
                        ->maxLength(255)
                        ->helperText('Dinamik ürün/blog kayıtları kendi modüllerindeki SEO alanlarından yönetilir.'),
                    TextInput::make('route_name')
                        ->label('Rota adı')
                        ->maxLength(255),
                    TextInput::make('schema_type')
                        ->label('Schema tipi')
                        ->placeholder('WebPage, Article, Product')
                        ->maxLength(255),
                ]),
            Section::make('Meta Bilgiler')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('Meta başlık')->maxLength(255),
                    TextInput::make('canonical_url')->label('Canonical adres')->url()->maxLength(255),
                    Textarea::make('description')->label('Meta açıklama')->rows(3)->maxLength(320)->columnSpanFull(),
                    Select::make('robots')
                        ->label('Robots etiketi')
                        ->options([
                            'index,follow' => 'Dizine açık, takip açık',
                            'noindex,follow' => 'Dizine kapalı, takip açık',
                            'noindex,nofollow' => 'Dizine kapalı, takip kapalı',
                        ])
                        ->default('index,follow'),
                    TextInput::make('og_title')->label('OG başlık')->maxLength(255),
                    Textarea::make('og_description')->label('OG açıklama')->rows(2)->maxLength(320)->columnSpanFull(),
                    FileUpload::make('og_image')
                        ->label('OG görsel')
                        ->disk('public')
                        ->directory('media/og')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                        ->maxSize(4096),
                    KeyValue::make('schema_payload')
                        ->label('Ek schema verisi')
                        ->keyLabel('Alan')
                        ->valueLabel('Değer')
                        ->columnSpanFull(),
                ]),
            SchemaBlockFields::section('page'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('path')->label('Sayfa')->searchable()->sortable(),
                TextColumn::make('route_name')->label('Rota')->searchable()->toggleable(),
                TextColumn::make('title')->label('Meta başlık')->searchable()->limit(55),
                TextColumn::make('robots')
                    ->label('Robots etiketi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'index,follow' => 'Dizine açık, takip açık',
                        'noindex,follow' => 'Dizine kapalı, takip açık',
                        'noindex,nofollow' => 'Dizine kapalı, takip kapalı',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('robots')
                    ->label('Robots')
                    ->options([
                        'index,follow' => 'Dizine açık, takip açık',
                        'noindex,follow' => 'Dizine kapalı, takip açık',
                        'noindex,nofollow' => 'Dizine kapalı, takip kapalı',
                    ]),
            ])
            ->recordActions([
                Action::make('view_site')
                    ->label('Ön yüzde gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (SeoEntry $record): string => url($record->path ?: '/'), shouldOpenInNewTab: true),
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->defaultSort('path');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoEntries::route('/'),
            'create' => CreateSeoEntry::route('/create'),
            'edit' => EditSeoEntry::route('/{record}/edit'),
        ];
    }
}
