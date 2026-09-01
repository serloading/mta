<?php

namespace App\Filament\Resources\ScopeCategories;

use App\Filament\Resources\ScopeCategories\Pages\CreateScopeCategory;
use App\Filament\Resources\ScopeCategories\Pages\EditScopeCategory;
use App\Filament\Resources\ScopeCategories\Pages\ListScopeCategories;
use App\Models\ScopeCategory;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class ScopeCategoryResource extends Resource
{
    protected static ?string $model = ScopeCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?string $navigationLabel = 'Kapsam Kategorileri';

    protected static ?string $modelLabel = 'kapsam kategorisi';

    protected static ?string $pluralModelLabel = 'kapsam kategorileri';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kapsam Ölçüm Alanı')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->maxLength(120)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set, $context) => $context === 'create' ? $set('slug', Str::slug($state)) : null),
                    TextInput::make('slug')
                        ->label('Slug (URL/filtre anahtarı)')
                        ->required()
                        ->maxLength(120)
                        ->unique(ignoreRecord: true)
                        ->helperText('Sayfadaki filtre çipiyle eşleşir. Örn: sicaklik, boyut, basinc.'),
                    TextInput::make('icon')
                        ->label('İkon (emoji)')
                        ->maxLength(8)
                        ->placeholder('🌡️'),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                    Textarea::make('summary')
                        ->label('Kısa açıklama')
                        ->rows(2)
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->helperText('Kategori başlığının altında görünür. "· N grup" otomatik eklenir.'),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')->label('İkon'),
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable()->toggleable(),
                TextColumn::make('groups_count')->counts('groups')->label('Grup'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->filters([TernaryFilter::make('is_active')->label('Aktif')])
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
            'index' => ListScopeCategories::route('/'),
            'create' => CreateScopeCategory::route('/create'),
            'edit' => EditScopeCategory::route('/{record}/edit'),
        ];
    }
}
