<?php

namespace App\Filament\Resources\Articles;

use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Filament\Support\SchemaBlockFields;
use App\Models\Article;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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
use Illuminate\Support\Str;
use UnitEnum;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Blog İçerikleri';

    protected static ?string $modelLabel = 'Blog yazısı';

    protected static ?string $pluralModelLabel = 'Blog içerikleri';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Yayın Bilgileri')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')
                        ->label('Kısa bağlantı adı')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Select::make('status')
                        ->label('Durum')
                        ->options([
                            'draft' => 'Taslak',
                            'published' => 'Yayında',
                            'archived' => 'Arşiv',
                        ])
                        ->default('draft')
                        ->required(),
                    DateTimePicker::make('published_at')
                        ->label('Yayın tarihi')
                        ->seconds(false)
                        ->default(now())
                        ->helperText('İleri tarih verirseniz yazı o tarihte otomatik yayınlanır.'),
                    Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'Kalibrasyon Rehberleri' => 'Kalibrasyon Rehberleri',
                            'Satın Alma Rehberleri' => 'Satın Alma Rehberleri',
                            'Laboratuvar Cihazları' => 'Laboratuvar Cihazları',
                            'Cihaz Tanıtımları' => 'Cihaz Tanıtımları',
                            'Teknik Servis ve Bakım' => 'Teknik Servis ve Bakım',
                            'Ölçüm Güvenilirliği' => 'Ölçüm Güvenilirliği',
                            'Sektör ve Uygulama' => 'Sektör ve Uygulama',
                            'Haberler' => 'Haberler',
                        ])
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('category_slug', Str::slug((string) $state))),
                    TextInput::make('category_slug')
                        ->label('Kategori kısa bağlantı adı')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Kategori seçilince otomatik dolar; gerekirse düzenleyin.'),
                    TextInput::make('author')
                        ->label('Yazar')
                        ->default('MTA Teknik Editör')
                        ->maxLength(255),
                    TextInput::make('reading_time')
                        ->label('Okuma süresi')
                        ->placeholder('4 dk')
                        ->helperText('Boş bırakılırsa içerik uzunluğundan otomatik hesaplanır.')
                        ->maxLength(255),
                    Repeater::make('tags')
                        ->label('Etiketler')
                        ->simple(TextInput::make('tag')->maxLength(80))
                        ->columnSpanFull(),
                ]),
            Section::make('İçerik')
                ->schema([
                    Textarea::make('excerpt')
                        ->label('Özet')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    FileUpload::make('image')
                        ->label('Kapak görseli')
                        ->disk('public')
                        ->directory('media/blog')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                        ->maxSize(4096),
                    TextInput::make('image_alt')
                        ->label('Görsel alternatif metni')
                        ->maxLength(255),
                    RichEditor::make('body')
                        ->label('İçerik editörü')
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('SEO')
                ->columns(2)
                ->schema([
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
                    TextInput::make('og_title')->label('OG başlık')->maxLength(255),
                    Textarea::make('og_description')->label('OG açıklama')->rows(2)->maxLength(320)->columnSpanFull(),
                    FileUpload::make('og_image')
                        ->label('OG görsel')
                        ->disk('public')
                        ->directory('media/og')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                        ->maxSize(4096),
                ]),
            SchemaBlockFields::section('article'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->searchable()->toggleable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'published' => 'Yayında',
                        'archived' => 'Arşiv',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('published_at')->label('Yayın')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşiv']),
                SelectFilter::make('category')->label('Kategori')->options(fn () => Article::query()->pluck('category', 'category')->filter()->all()),
            ])
            ->recordActions([
                Action::make('view_site')
                    ->label('Ön yüzde gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Article $record): string => route('knowledge.show', $record->slug), shouldOpenInNewTab: true),
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
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'edit' => EditArticle::route('/{record}/edit'),
        ];
    }
}
