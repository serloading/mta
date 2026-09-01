<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Support\SchemaBlockFields;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocument;

    protected static ?string $navigationLabel = 'İçerik Sayfaları';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sayfa İçeriği')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('Başlık')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')->label('Kısa bağlantı adı')->required()->unique(ignoreRecord: true)->maxLength(255),
                    Select::make('status')->label('Durum')->options(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşiv'])->default('draft')->required(),
                    DateTimePicker::make('published_at')->label('Yayın tarihi')->seconds(false),
                    TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0),
                    FileUpload::make('image')->label('Görsel')->disk('public')->directory('media/pages')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(4096),
                    Textarea::make('excerpt')->label('Özet')->rows(3)->columnSpanFull(),
                    RichEditor::make('body')->label('Ana içerik')->columnSpanFull(),
                ]),
            Section::make('SEO')
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')->label('Meta başlık')->maxLength(255),
                    TextInput::make('canonical_url')->label('Canonical adres')->url()->maxLength(255),
                    Textarea::make('meta_description')->label('Meta açıklama')->rows(3)->maxLength(320)->columnSpanFull(),
                    Select::make('robots')->label('Robots etiketi')->options(['index,follow' => 'Dizine açık, takip açık', 'noindex,follow' => 'Dizine kapalı, takip açık', 'noindex,nofollow' => 'Dizine kapalı, takip kapalı'])->default('index,follow'),
                    TextInput::make('og_title')->label('OG başlık')->maxLength(255),
                    Textarea::make('og_description')->label('OG açıklama')->rows(2)->maxLength(320)->columnSpanFull(),
                    FileUpload::make('og_image')->label('OG görsel')->disk('public')->directory('media/og')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(4096),
                ]),
            SchemaBlockFields::section('page'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Sayfa')->searchable()->sortable(),
                TextColumn::make('slug')->label('Kısa bağlantı adı')->searchable(),
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
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Durum')->options(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşiv']),
            ])
            ->recordActions([
                Action::make('view_site')
                    ->label('Ön yüzde gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Page $record): string => url('/' . ltrim($record->slug, '/')), shouldOpenInNewTab: true),
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
