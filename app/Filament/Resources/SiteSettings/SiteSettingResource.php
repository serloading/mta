<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog8Tooth;

    protected static ?string $navigationLabel = 'Sosyal Medya ve Kodlar';

    protected static ?string $modelLabel = 'Site ayarı';

    protected static ?string $pluralModelLabel = 'Site ayarları';

    protected static string|UnitEnum|null $navigationGroup = 'Site Ayarları';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ayar Tipi')
                ->columns(2)
                ->schema([
                    Select::make('key')
                        ->label('Ayar')
                        ->options([
                            'social_links' => 'Sosyal medya linkleri',
                            'tracking_codes' => 'Analitik ve doğrulama kodları',
                        ])
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    TextInput::make('group')
                        ->label('Grup')
                        ->disabled()
                        ->dehydrated(),
                ]),
            Section::make('Sosyal Medya Linkleri')
                ->description('Boş bırakılan sosyal medya linkleri ön yüzde gösterilmez.')
                ->schema([
                    Repeater::make('value.links')
                        ->label('Hesaplar')
                        ->addActionLabel('Hesap ekle')
                        ->reorderable()
                        ->schema([
                            TextInput::make('name')->label('Hesap adı')->required()->maxLength(80),
                            TextInput::make('label')->label('Kısa etiket')->maxLength(20),
                            TextInput::make('url')->label('Bağlantı adresi')->url()->maxLength(255),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ]),
            Section::make('Analitik ve Doğrulama Kodları')
                ->description('Google Analytics, Google Search Console, Bing doğrulama meta etiketi ve benzeri kodları buradan yönetebilirsiniz.')
                ->schema([
                    CodeEditor::make('value.verification_meta')
                        ->label('Doğrulama meta etiketleri')
                        ->language(Language::Html)
                        ->columnSpanFull(),
                    CodeEditor::make('value.head_scripts')
                        ->label('Sayfa başlığı içi kodlar')
                        ->language(Language::Html)
                        ->columnSpanFull(),
                    CodeEditor::make('value.body_start_scripts')
                        ->label('Sayfa gövdesi başlangıç kodları')
                        ->language(Language::Html)
                        ->columnSpanFull(),
                    CodeEditor::make('value.body_end_scripts')
                        ->label('Sayfa gövdesi bitiş kodları')
                        ->language(Language::Html)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Ayar')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'social_links' => 'Sosyal medya linkleri',
                        'tracking_codes' => 'Analitik ve doğrulama kodları',
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ])
            ->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteSettings::route('/'),
            'edit' => EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
