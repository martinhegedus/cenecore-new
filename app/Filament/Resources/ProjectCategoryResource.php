<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCategoryResource\Pages\CreateProjectCategory;
use App\Filament\Resources\ProjectCategoryResource\Pages\EditProjectCategory;
use App\Filament\Resources\ProjectCategoryResource\Pages\ListProjectCategories;
use App\Filament\Resources\Support\ResourceTranslations;
use App\Models\ProjectCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProjectCategoryResource extends Resource
{
    protected static ?string $model = ProjectCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Kategórie projektov';

    protected static ?string $modelLabel = 'kategória projektu';

    protected static ?string $pluralModelLabel = 'kategórie projektov';

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nastavenia kategórie')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktívna')
                            ->default(true),
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
                TextColumn::make('sk_name')
                    ->label('Názov')
                    ->state(fn (ProjectCategory $record): ?string => $record->translation('sk')->first()?->name)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'translations',
                        fn (Builder $query): Builder => $query->where('locale', 'sk')->where('name', 'like', "%{$search}%"),
                    )),
                IconColumn::make('is_active')
                    ->label('Aktívna')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Poradie')
                    ->sortable(),
                TextColumn::make('missing_translations')
                    ->label('Preklady')
                    ->state(fn (ProjectCategory $record): string => PageResource::translationSummary($record))
                    ->badge(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Aktívna'),
                Filter::make('missing_cs')
                    ->label('Chýba český preklad')
                    ->query(fn (Builder $query): Builder => $query->whereDoesntHave('translations', fn (Builder $query): Builder => $query->where('locale', 'cs'))),
                Filter::make('missing_en')
                    ->label('Chýba anglický preklad')
                    ->query(fn (Builder $query): Builder => $query->whereDoesntHave('translations', fn (Builder $query): Builder => $query->where('locale', 'en'))),
            ])
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
            'index' => ListProjectCategories::route('/'),
            'create' => CreateProjectCategory::route('/create'),
            'edit' => EditProjectCategory::route('/{record}/edit'),
        ];
    }

    public static function translationFields(): array
    {
        return ['slug', 'name', 'description', 'seo_title', 'seo_description'];
    }

    public static function translationTable(): string
    {
        return 'project_category_translations';
    }

    public static function translationForeignKey(): string
    {
        return 'project_category_id';
    }
}
