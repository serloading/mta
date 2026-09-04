<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Support\SchemaBlockFields;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Kalibrasyon Hizmetleri';

    protected static ?string $modelLabel = 'kalibrasyon hizmeti';

    protected static ?string $pluralModelLabel = 'kalibrasyon hizmetleri';

    protected static string|UnitEnum|null $navigationGroup = 'Hizmetler';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hizmet')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('Başlık')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')->label('Kısa bağlantı adı')->required()->unique(ignoreRecord: true)->maxLength(255),
                    TextInput::make('category')->label('Kategori')->maxLength(255),
                    TextInput::make('eyebrow')->label('Üst başlık')->maxLength(255),
                    Textarea::make('summary')->label('Özet')->rows(3)->columnSpanFull(),
                    Textarea::make('answer')->label('Kısa cevap')->rows(3)->columnSpanFull(),
                    RichEditor::make('body')->label('Ana içerik')->columnSpanFull(),
                    FileUpload::make('image')->label('Görsel')->disk('public')->directory('media/services')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(4096),
                    TextInput::make('image_alt')->label('Alt text')->maxLength(255),
                    TextInput::make('cta')->label('Çağrı butonu metni')->maxLength(255),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                ]),
            Section::make('Kapsam')
                ->columns(2)
                ->schema([
                    TagsInput::make('devices')->label('Cihazlar')->columnSpanFull(),
                    TagsInput::make('applications')->label('Uygulamalar')->columnSpanFull(),
                    TagsInput::make('standards')->label('Standartlar')->columnSpanFull(),
                    KeyValue::make('capacity')->label('Teknik kapasite')->keyLabel('Alan')->valueLabel('Değer')->columnSpanFull(),
                    Repeater::make('process_steps')
                        ->label('Süreç adımları')
                        ->simple(TextInput::make('step')->maxLength(160))
                        ->columnSpanFull(),
                    Repeater::make('faq')
                        ->label('Hizmet SSS')
                        ->schema([
                            TextInput::make('question')->label('Soru')->required(),
                            Textarea::make('answer')->label('Cevap')->required()->rows(2),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                    Select::make('productCategories')->label('İlgili ürün kategorileri')->relationship('productCategories', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                ]),
            Section::make('SEO')
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')->label('Meta başlık')->maxLength(255),
                    Textarea::make('meta_description')->label('Meta açıklama')->rows(3)->maxLength(320)->columnSpanFull(),
                ]),
            SchemaBlockFields::section('service'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Hizmet')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->toggleable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->recordActions([
                Action::make('view_site')
                    ->label('Ön yüzde gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Service $record): string => route('services.show', $record->slug), shouldOpenInNewTab: true),
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
