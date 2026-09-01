<?php

namespace App\Filament\Resources\ScopeGroups;

use App\Filament\Resources\ScopeGroups\Pages\CreateScopeGroup;
use App\Filament\Resources\ScopeGroups\Pages\EditScopeGroup;
use App\Filament\Resources\ScopeGroups\Pages\ListScopeGroups;
use App\Models\ScopeGroup;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class ScopeGroupResource extends Resource
{
    protected static ?string $model = ScopeGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $navigationLabel = 'Kapsam Grupları';

    protected static ?string $modelLabel = 'kapsam grubu';

    protected static ?string $pluralModelLabel = 'kapsam grupları';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 41;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Cihaz Grubu')
                ->columns(2)
                ->schema([
                    Select::make('scope_category_id')
                        ->label('Kapsam kategorisi')
                        ->relationship('category', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('title')->label('Grup başlığı')->required()->maxLength(200)->columnSpanFull(),
                    TextInput::make('key')
                        ->label('Anahtar (anchor id)')
                        ->maxLength(80)
                        ->helperText('Sayfada #anchor ve "teklif iste" linkinde kullanılır. Boşsa otomatik atanır. Örn: sicaklik-1.'),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                    TagsInput::make('columns')
                        ->label('Kolon başlıkları')
                        ->placeholder('Aralık, Ortam, U, Yöntem, Yer')
                        ->columnSpanFull()
                        ->helperText('Tablo kolonlarının başlıkları. Sırayla girin.'),
                    Textarea::make('rows')
                        ->label('Ölçüm satırları')
                        ->rows(14)
                        ->columnSpanFull()
                        ->helperText('Her satır bir ölçüm satırı. Hücreleri " | " ile ayırın. Hücre sayısı kolon sayısıyla aynı olmalı. Değer yoksa — yazın.')
                        ->formatStateUsing(fn ($state) => collect(is_array($state) ? $state : [])
                            ->map(fn ($row) => implode(' | ', array_map('strval', (array) $row)))
                            ->implode("\n"))
                        ->dehydrateStateUsing(fn ($state) => collect(preg_split('/\r\n|\r|\n/', (string) $state))
                            ->map(fn ($line) => trim($line))
                            ->filter()
                            ->map(fn ($line) => array_map('trim', explode('|', $line)))
                            ->values()
                            ->all()),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.title')->label('Kategori')->sortable()->searchable(),
                TextColumn::make('title')->label('Grup')->searchable()->sortable()->wrap(),
                TextColumn::make('row_count')->label('Satır')->state(fn (ScopeGroup $record) => is_array($record->rows) ? count($record->rows) : 0),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->filters([
                SelectFilter::make('scope_category_id')->label('Kategori')->relationship('category', 'title'),
                TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->defaultSort('scope_category_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScopeGroups::route('/'),
            'create' => CreateScopeGroup::route('/create'),
            'edit' => EditScopeGroup::route('/{record}/edit'),
        ];
    }
}
