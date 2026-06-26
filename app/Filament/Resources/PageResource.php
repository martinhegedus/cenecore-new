<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Filament\Resources\Support\ResourceTranslations;
use App\Models\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Stránky';

    protected static ?string $modelLabel = 'stránka';

    protected static ?string $pluralModelLabel = 'stránky';

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nastavenia stránky')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('type')
                            ->label('Typ')
                            ->options([
                                'homepage' => 'Domovská stránka',
                                'standard' => 'Štandardná stránka',
                                'landing' => 'Landing stránka',
                            ])
                            ->native(false),
                        Select::make('status')
                            ->label('Stav')
                            ->options(self::statusOptions())
                            ->default('draft')
                            ->required()
                            ->native(false),
                        Toggle::make('is_indexable')
                            ->label('Indexovateľná')
                            ->default(true),
                        DateTimePicker::make('published_at')
                            ->label('Publikované'),
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
                    ->state(fn (Page $record): ?string => $record->translation('sk')->first()?->title)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'translations',
                        fn (Builder $query): Builder => $query->where('locale', 'sk')->where('title', 'like', "%{$search}%"),
                    )),
                TextColumn::make('type')
                    ->label('Typ')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publikované')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_indexable')
                    ->label('Index')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Upravené')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('missing_translations')
                    ->label('Preklady')
                    ->state(fn (Page $record): string => self::translationSummary($record))
                    ->badge(),
            ])
            ->filters(self::translationFilters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(self::statusOptions()),
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

    public static function statusOptions(): array
    {
        return [
            'draft' => 'Koncept',
            'published' => 'Publikované',
        ];
    }

    public static function translationFields(): array
    {
        return ['slug', 'title', 'excerpt', 'body', 'seo_title', 'seo_description', 'og_title', 'og_description', 'canonical_url'];
    }

    public static function translationTable(): string
    {
        return 'page_translations';
    }

    public static function translationForeignKey(): string
    {
        return 'page_id';
    }

    public static function translationSummary(Model $record): string
    {
        $locales = $record->translations->pluck('locale')->all();
        $missing = collect(['cs' => 'CS', 'en' => 'EN'])
            ->reject(fn (string $label, string $locale): bool => in_array($locale, $locales, true))
            ->values()
            ->all();

        return $missing === [] ? 'OK' : 'Chýba '.implode(', ', $missing);
    }

    public static function translationFilters(array $filters = []): array
    {
        return [
            ...$filters,
            Filter::make('missing_cs')
                ->label('Chýba český preklad')
                ->query(fn (Builder $query): Builder => $query->whereDoesntHave('translations', fn (Builder $query): Builder => $query->where('locale', 'cs'))),
            Filter::make('missing_en')
                ->label('Chýba anglický preklad')
                ->query(fn (Builder $query): Builder => $query->whereDoesntHave('translations', fn (Builder $query): Builder => $query->where('locale', 'en'))),
        ];
    }
}
