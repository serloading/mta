<?php

namespace App\Filament\Resources\Products;

use App\Filament\Support\SchemaBlockFields;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Ürün Yönetimi';

    protected static ?string $modelLabel = 'Ürün';

    protected static ?string $pluralModelLabel = 'Ürünler';

    protected static string|UnitEnum|null $navigationGroup = 'Katalog';

    /**
     * Keep the admin-chosen file name (slugified, lower-case) instead of a random hash,
     * so a file uploaded as "shimadzu-moc-63u.jpg" stays at that path.
     * Same base name uploaded again to the same folder overwrites the previous file.
     */
    protected static function keepUploadedFileName(): \Closure
    {
        return function (TemporaryUploadedFile $file): string {
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = Str::lower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');

            return ($name !== '' ? $name : 'dosya') . '.' . $extension;
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Ürün')
                ->persistTabInQueryString()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Genel')
                        ->icon(Heroicon::OutlinedInformationCircle)
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Ürün adı')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state)))
                                ->columnSpanFull(),
                            TextInput::make('slug')
                                ->label('Kısa bağlantı adı')
                                ->required()
                                ->maxLength(255)
                                ->helperText('Ürün adından otomatik türetilir; gerekirse elle düzenleyin.'),
                            Select::make('status')
                                ->label('Durum')
                                ->options([
                                    'draft' => 'Taslak',
                                    'published' => 'Aktif',
                                    'archived' => 'Arşiv',
                                ])
                                ->default('draft')
                                ->required(),
                            Select::make('product_category_id')
                                ->label('Kategori')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('product_brand_id')
                                ->label('Marka')
                                ->relationship('brand', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('model')->label('Model')->maxLength(255),
                            TextInput::make('sku')->label('Ürün kodu')->maxLength(255),
                            DateTimePicker::make('published_at')->label('Yayın tarihi')->seconds(false),
                            Toggle::make('is_featured')->label('Öne çıkan')->inline(false),
                            TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0),
                        ]),
                    Tab::make('İçerik')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema([
                            Textarea::make('summary')->label('Kısa açıklama')->rows(3)->maxLength(600)->columnSpanFull(),
                            RichEditor::make('body')->label('Uzun açıklama')->columnSpanFull(),
                            TagsInput::make('features')
                                ->label('Öne çıkan özellik maddeleri')
                                ->helperText('Ürün sayfasında "Öne Çıkan Avantajlar" listesinde görünür.')
                                ->columnSpanFull(),
                            KeyValue::make('specs')
                                ->label('Teknik özellik tablosu')
                                ->keyLabel('Başlık')
                                ->valueLabel('Değer')
                                ->reorderable()
                                ->live(onBlur: true)
                                ->columnSpanFull(),
                            CheckboxList::make('filter_keys')
                                ->label('Filtrede gösterilecek özellikler')
                                ->helperText('İşaretlenen başlıklar marka / kategori sayfalarındaki sol filtre panelinde seçenek olarak çıkar. Boş bırakılırsa filtre otomatik davranışına döner.')
                                ->options(fn (Get $get): array => collect($get('specs') ?? [])
                                    ->keys()
                                    ->filter(fn ($key) => filled($key))
                                    ->mapWithKeys(fn ($key) => [$key => $key])
                                    ->all())
                                ->columns(2)
                                ->gridDirection('row')
                                ->columnSpanFull(),
                            KeyValue::make('metadata')
                                ->label('Ek veri alanları')
                                ->keyLabel('Alan')
                                ->valueLabel('Değer')
                                ->columnSpanFull(),
                            Select::make('services')
                                ->label('Ürüne özel ilgili kalibrasyon hizmetleri')
                                ->relationship('services', 'title')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Boş bırakılırsa kategoriye bağlı hizmetler kullanılır.')
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Görseller')
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            FileUpload::make('image')
                                ->label('Ana görsel')
                                ->disk('public')
                                ->directory('media/products')
                                ->getUploadedFileNameForStorageUsing(static::keepUploadedFileName())
                                ->image()
                                ->imageEditor()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                                ->maxSize(6144)
                                ->helperText('Tek ana ürün görseli. Yüklediğiniz dosya adı korunur (slug\'a dönüştürülür). Önerilen: kare veya 4:3, sade / şeffaf zemin.')
                                ->columnSpanFull(),
                            TextInput::make('image_alt')
                                ->label('Ana görsel alternatif metni')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            FileUpload::make('gallery')
                                ->label('Ek galeri görselleri')
                                ->disk('public')
                                ->directory('media/products/gallery')
                                ->getUploadedFileNameForStorageUsing(static::keepUploadedFileName())
                                ->multiple()
                                ->reorderable()
                                ->image()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                                ->maxSize(6144)
                                ->helperText('Ana görsel dışındaki ek görseller. Dosya adları korunur. Sürükleyerek sıralayabilirsiniz.')
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Doküman & Video')
                        ->icon(Heroicon::OutlinedFilm)
                        ->schema([
                            Repeater::make('documents')
                                ->label('Doküman / teknik dosya')
                                ->relationship()
                                ->schema([
                                    TextInput::make('title')->label('Başlık')->required()->maxLength(255),
                                    Select::make('type')
                                        ->label('Tip')
                                        ->options([
                                            'catalog' => 'Katalog',
                                            'datasheet' => 'Teknik veri sayfası',
                                            'manual' => 'Kullanım kılavuzu',
                                            'certificate' => 'Sertifika',
                                        ])
                                        ->default('catalog'),
                                    FileUpload::make('path')
                                        ->label('Dosya (PDF / Word)')
                                        ->disk('public')
                                        ->directory('media/documents')
                                        ->getUploadedFileNameForStorageUsing(static::keepUploadedFileName())
                                        ->acceptedFileTypes([
                                            'application/pdf',
                                            'application/msword',
                                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        ])
                                        ->maxSize(20480)
                                        ->helperText('PDF veya Word (.doc/.docx). Yüklediğiniz dosya adı korunur.'),
                                    TextInput::make('url')->label('Dış bağlantı adresi')->url()->maxLength(255)
                                        ->helperText('Dosya yüklemek yerine dış bir bağlantı verebilirsiniz.'),
                                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                                ])
                                ->columns(2)
                                ->collapsible()
                                ->collapsed()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Yeni doküman')
                                ->addActionLabel('Doküman ekle')
                                ->columnSpanFull(),
                            Repeater::make('videos')
                                ->label('Ürün videoları')
                                ->relationship()
                                ->schema([
                                    TextInput::make('title')->label('Video başlığı')->required()->maxLength(255),
                                    TextInput::make('youtube_url')->label('YouTube linki')->url()->maxLength(255),
                                    TextInput::make('youtube_id')->label('YouTube video kodu')->maxLength(32),
                                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                                ])
                                ->columns(2)
                                ->collapsible()
                                ->collapsed()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Yeni video')
                                ->addActionLabel('Video ekle')
                                ->columnSpanFull(),
                        ]),
                    Tab::make('SEO & Schema')
                        ->icon(Heroicon::OutlinedMagnifyingGlass)
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('seo_title')->label('Meta başlık')->maxLength(255),
                                TextInput::make('canonical_url')->label('Canonical adres')->url()->maxLength(255),
                                Textarea::make('meta_description')->label('Meta açıklama')->rows(3)->maxLength(320)->columnSpanFull(),
                                Select::make('robots')
                                    ->label('Robots etiketi')
                                    ->options([
                                        'index,follow' => 'Dizine açık, takip açık',
                                        'noindex,follow' => 'Dizine kapalı, takip açık',
                                        'noindex,nofollow' => 'Dizine kapalı, takip kapalı',
                                    ])
                                    ->default('index,follow'),
                            ]),
                            SchemaBlockFields::section('product'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ürün')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori')->sortable()->toggleable(),
                TextColumn::make('brand.name')->label('Marka')->sortable()->toggleable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'published' => 'Aktif',
                        'archived' => 'Arşiv',
                        default => $state,
                    })
                    ->sortable(),
                IconColumn::make('is_featured')->label('Öne çıkan')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(['draft' => 'Taslak', 'published' => 'Aktif', 'archived' => 'Arşiv']),
                SelectFilter::make('product_category_id')->label('Kategori')->relationship('category', 'name')->searchable()->preload(),
                SelectFilter::make('product_brand_id')->label('Marka')->relationship('brand', 'name')->searchable()->preload(),
                TernaryFilter::make('is_featured')->label('Öne çıkan'),
            ])
            ->recordActions([
                Action::make('view_site')
                    ->label('Ön yüzde gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Product $record): string => route('products.show', $record->slug), shouldOpenInNewTab: true),
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
