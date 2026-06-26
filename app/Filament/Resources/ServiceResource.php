<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Filament\Resources\Support\ResourceTranslations;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Služby';

    protected static ?string $modelLabel = 'služba';

    protected static ?string $pluralModelLabel = 'služby';

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nastavenia služby')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('icon')
                            ->label('Ikona')
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('Odporúčaná')
                            ->default(false),
                        Select::make('status')
                            ->label('Stav')
                            ->options(PageResource::statusOptions())
                            ->default('draft')
                            ->required()
                            ->native(false),
                        DateTimePicker::make('published_at')
                            ->label('Publikované'),
                    ]),
                ]),
            Section::make('Médiá')
                ->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('cover_upload')
                            ->label('Titulný obrázok')
                            ->image()
                            ->imagePreviewHeight('180')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->storeFiles(false),
                        FileUpload::make('og_upload')
                            ->label('OG obrázok')
                            ->image()
                            ->imagePreviewHeight('180')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->storeFiles(false),
                    ]),
                ]),
            ResourceTranslations::tabs(self::translationFields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('translations'))
            ->columns([
                TextColumn::make('sk_title')
                    ->label('Názov')
                    ->state(fn (Service $record): ?string => $record->translation('sk')->first()?->title)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'translations',
                        fn (Builder $query): Builder => $query->where('locale', 'sk')->where('title', 'like', "%{$search}%"),
                    )),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => PageResource::statusOptions()[$state] ?? $state)
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Odporúčaná')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Poradie')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publikované')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('missing_translations')
                    ->label('Preklady')
                    ->state(fn (Service $record): string => PageResource::translationSummary($record))
                    ->badge(),
            ])
            ->filters(PageResource::translationFilters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(PageResource::statusOptions()),
                TernaryFilter::make('is_featured')
                    ->label('Odporúčaná'),
            ]))
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
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

    public static function translationFields(): array
    {
        return ['slug', 'title', 'short_title', 'excerpt', 'body', 'seo_title', 'seo_description', 'og_title', 'og_description', 'canonical_url'];
    }

    public static function translationTable(): string
    {
        return 'service_translations';
    }

    public static function translationForeignKey(): string
    {
        return 'service_id';
    }
}
