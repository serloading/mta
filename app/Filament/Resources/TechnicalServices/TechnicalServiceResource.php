<?php

namespace App\Filament\Resources\TechnicalServices;

use App\Filament\Resources\TechnicalServices\Pages\CreateTechnicalService;
use App\Filament\Resources\TechnicalServices\Pages\EditTechnicalService;
use App\Filament\Resources\TechnicalServices\Pages\ListTechnicalServices;
use App\Filament\Support\SchemaBlockFields;
use App\Models\TechnicalService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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

class TechnicalServiceResource extends Resource
{
    protected static ?string $model = TechnicalService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Teknik Servis';

    protected static string|UnitEnum|null $navigationGroup = 'Hizmetler';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Teknik Servis')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('Başlık')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')->label('Kısa bağlantı adı')->required()->unique(ignoreRecord: true)->maxLength(255),
                    TextInput::make('category')->label('Kategori')->maxLength(255),
                    Textarea::make('summary')->label('Özet')->rows(3)->columnSpanFull(),
                    Textarea::make('answer')->label('Kısa cevap')->rows(3)->columnSpanFull(),
                    RichEditor::make('body')->label('Ana içerik')->columnSpanFull(),
                    FileUpload::make('image')->label('Görsel')->disk('public')->directory('media/technical-services')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(4096),
                    TextInput::make('image_alt')->label('Alt text')->maxLength(255),
                    TextInput::make('cta')->label('Çağrı butonu metni')->maxLength(255),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                ]),
            Section::make('Kapsam')
                ->schema([
                    Repeater::make('devices')->label('Servis verilen cihazlar')->simple(TextInput::make('device')->maxLength(160))->columnSpanFull(),
                    Repeater::make('service_steps')->label('Servis adımları')->simple(TextInput::make('step')->maxLength(160))->columnSpanFull(),
                    Repeater::make('advantages')->label('Avantajlar')->simple(TextInput::make('advantage')->maxLength(160))->columnSpanFull(),
                    Repeater::make('faq')
                        ->label('Teknik servis SSS')
                        ->schema([
                            TextInput::make('question')->label('Soru')->required(),
                            Textarea::make('answer')->label('Cevap')->required()->rows(2),
                        ])
                        ->columnSpanFull(),
                ]),
            Section::make('SEO')
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')->label('Meta başlık')->maxLength(255),
                    Textarea::make('meta_description')->label('Meta açıklama')->rows(3)->maxLength(320)->columnSpanFull(),
                ]),
            SchemaBlockFields::section('technical_service'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Teknik servis')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->toggleable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->recordActions([
                Action::make('view_site')
                    ->label('Ön yüzde gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (TechnicalService $record): string => route('technical-services.show', $record->slug), shouldOpenInNewTab: true),
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTechnicalServices::route('/'),
            'create' => CreateTechnicalService::route('/create'),
            'edit' => EditTechnicalService::route('/{record}/edit'),
        ];
    }
}
