<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
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
use UnitEnum;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $navigationLabel = 'Teklif Talepleri';

    protected static ?string $modelLabel = 'teklif talebi';

    protected static ?string $pluralModelLabel = 'teklif talepleri';

    protected static string|UnitEnum|null $navigationGroup = 'Satış';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Talep')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Ad Soyad')->required()->maxLength(255),
                    TextInput::make('company')->label('Firma')->maxLength(255),
                    TextInput::make('phone')->label('Telefon')->required()->maxLength(255),
                    TextInput::make('email')->label('E-posta')->email()->maxLength(255),
                    Textarea::make('message')->label('Mesaj')->rows(5)->required()->columnSpanFull(),
                    Select::make('status')->label('Durum')->options([
                        'new' => 'Yeni',
                        'contacted' => 'İletişime geçildi',
                        'quoted' => 'Teklif verildi',
                        'won' => 'Kazanıldı',
                        'lost' => 'Kaybedildi',
                        'spam' => 'Spam',
                    ])->default('new')->required(),
                    TextInput::make('source_type')->label('Kaynak tipi')->maxLength(255),
                    TextInput::make('source_url')->label('Kaynak bağlantı adresi')->url()->maxLength(255)->columnSpanFull(),
                    Textarea::make('notes')->label('İç not')->rows(4)->columnSpanFull(),
                ]),
            Section::make('İlişkiler ve İzleme')
                ->columns(2)
                ->schema([
                    Select::make('product_id')->label('Ürün')->relationship('product', 'name')->searchable()->preload(),
                    Select::make('service_id')->label('Kalibrasyon hizmeti')->relationship('service', 'title')->searchable()->preload(),
                    Select::make('technical_service_id')->label('Teknik servis')->relationship('technicalService', 'title')->searchable()->preload(),
                KeyValue::make('utm')->label('Kampanya izleme verisi')->keyLabel('Alan')->valueLabel('Değer')->columnSpanFull(),
                    KeyValue::make('payload')->label('Ek form verisi')->keyLabel('Alan')->valueLabel('Değer')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('name')->label('Ad Soyad')->searchable()->sortable(),
                TextColumn::make('company')->label('Firma')->searchable()->toggleable(),
                TextColumn::make('phone')->label('Telefon')->searchable(),
                TextColumn::make('source_type')->label('Kaynak')->badge()->toggleable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Yeni',
                        'contacted' => 'İletişime geçildi',
                        'quoted' => 'Teklif verildi',
                        'won' => 'Kazanıldı',
                        'lost' => 'Kaybedildi',
                        'spam' => 'Spam',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Durum')->options([
                    'new' => 'Yeni',
                    'contacted' => 'İletişime geçildi',
                    'quoted' => 'Teklif verildi',
                    'won' => 'Kazanıldı',
                    'lost' => 'Kaybedildi',
                    'spam' => 'Spam',
                ]),
            ])
            ->recordActions([EditAction::make()->label('Düzenle'), DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->toolbarActions([DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
