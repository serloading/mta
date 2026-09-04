<?php

namespace App\Filament\Resources\ProductBrands;

use App\Filament\Resources\ProductBrands\Pages\CreateProductBrand;
use App\Filament\Resources\ProductBrands\Pages\EditProductBrand;
use App\Filament\Resources\ProductBrands\Pages\ListProductBrands;
use App\Models\ProductBrand;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ProductBrandResource extends Resource
{
    protected static ?string $model = ProductBrand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Ürün Markaları';

    protected static ?string $modelLabel = 'Marka';

    protected static ?string $pluralModelLabel = 'Markalar';

    protected static string|UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Marka')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Ad')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')->label('Kısa bağlantı adı')->required()->unique(ignoreRecord: true)->maxLength(255),
                    Textarea::make('summary')->label('Özet')->rows(3)->columnSpanFull(),
                    FileUpload::make('logo')->label('Logo / Marka görseli')->disk('public')->directory('media/brands')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(4096),
                    TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    TagsInput::make('aliases')->label('Alternatif adlar')->columnSpanFull(),
                    Select::make('categories')->label('Bağlı kategoriler')->relationship('categories', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultPaginationPageOption(50)
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->getStateUsing(fn ($record): ?string => $record->logo ? \App\Support\Img::url($record->logo) : null)
                    ->height(30)->width(80),
                TextColumn::make('name')->label('Marka')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('products_count')->counts('products')->label('Ürün')->sortable()->badge(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([EditAction::make()->label('Düzenle'), DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->toolbarActions([DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductBrands::route('/'),
            'create' => CreateProductBrand::route('/create'),
            'edit' => EditProductBrand::route('/{record}/edit'),
        ];
    }
}
