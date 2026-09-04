<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Filament\Resources\Redirects\Pages\ListRedirects;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTurnRightUp;

    protected static ?string $navigationLabel = '301 Yönlendirmeler';

    protected static ?string $modelLabel = 'Yönlendirme';

    protected static ?string $pluralModelLabel = 'Yönlendirmeler';

    protected static string|UnitEnum|null $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Yönlendirme')
                ->columns(2)
                ->schema([
                    TextInput::make('source_path')
                        ->label('Eski bağlantı yolu')
                        ->placeholder('/eski-sayfa')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('target_path')
                        ->label('Yeni bağlantı yolu')
                        ->placeholder('/yeni-sayfa')
                        ->required()
                        ->maxLength(255),
                    Select::make('status_code')
                        ->label('Durum kodu')
                        ->options([301 => '301 Kalıcı', 302 => '302 Geçici'])
                        ->default(301)
                        ->required(),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source_path')->label('Eski bağlantı')->searchable()->sortable(),
                TextColumn::make('target_path')->label('Yeni bağlantı')->searchable(),
                TextColumn::make('status_code')->label('Kod')->badge()->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('updated_at')->label('Son düzenleme')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([TernaryFilter::make('is_active')->label('Aktif')])
            ->recordActions([EditAction::make()->label('Düzenle'), DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->toolbarActions([DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->defaultSort('source_path');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
