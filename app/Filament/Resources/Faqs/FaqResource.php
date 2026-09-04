<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
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
use UnitEnum;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $navigationLabel = 'SSS Yönetimi';

    protected static ?string $modelLabel = 'SSS kaydı';

    protected static ?string $pluralModelLabel = 'SSS kayıtları';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Soru Cevap')
                ->columns(2)
                ->schema([
                    TextInput::make('question')->label('Soru')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('answer')->label('Cevap')->required()->rows(4)->columnSpanFull(),
                    TextInput::make('group_key')->label('Grup anahtarı')->maxLength(255),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->label('Soru')->searchable()->sortable(),
                TextColumn::make('group_key')->label('Grup')->searchable()->toggleable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->filters([TernaryFilter::make('is_active')->label('Aktif')])
            ->recordActions([EditAction::make()->label('Düzenle'), DeleteAction::make()->label('Sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->toolbarActions([DeleteBulkAction::make()->label('Toplu sil')->visible(fn () => auth()->user()?->isAdmin())])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
