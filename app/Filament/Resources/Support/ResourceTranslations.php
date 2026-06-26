<?php

namespace App\Filament\Resources\Support;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ResourceTranslations
{
    public const LOCALES = [
        'sk' => 'Slovenčina',
        'cs' => 'Čeština',
        'en' => 'English',
    ];

    /**
     * @param  array<int, string>  $fields
     * @return array<string, array<string, mixed>>
     */
    public static function extract(array &$data, array $fields): array
    {
        $translations = $data['translations'] ?? [];

        unset($data['translations']);

        return collect(self::LOCALES)
            ->keys()
            ->mapWithKeys(fn (string $locale): array => [
                $locale => Arr::only($translations[$locale] ?? [], $fields),
            ])
            ->all();
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<string, array<string, mixed>>
     */
    public static function formData(Model $record, array $fields): array
    {
        $translations = $record->translations()
            ->get()
            ->keyBy('locale')
            ->map(fn (Model $translation): array => Arr::only($translation->attributesToArray(), $fields))
            ->all();

        foreach (array_keys(self::LOCALES) as $locale) {
            $translations[$locale] ??= array_fill_keys($fields, null);
        }

        return $translations;
    }

    /**
     * @param  array<string, array<string, mixed>>  $translations
     * @param  array<int, string>  $fields
     */
    public static function save(Model $record, array $translations, array $fields): void
    {
        foreach ($translations as $locale => $values) {
            $values = Arr::only($values, $fields);

            $hasContent = collect($values)->contains(fn (mixed $value): bool => self::hasMeaningfulValue($value));
            $exists = $record->translations()->where('locale', $locale)->exists();

            if (! $hasContent && ! $exists) {
                continue;
            }

            $record->translations()->updateOrCreate(
                ['locale' => $locale],
                $values,
            );
        }
    }

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    public static function validate(
        ?Model $record,
        array $translations,
        string $translationTable,
        string $foreignKey,
        bool $hasSlug,
        bool $requiresSlovakTranslation,
    ): void {
        $rules = [];

        if ($requiresSlovakTranslation) {
            $rules['translations.sk.slug'] = ['required'];
            $rules['translations.sk.title'] = ['required'];
            $rules['translations.sk.name'] = ['required_without:translations.sk.title'];
        }

        if ($hasSlug) {
            foreach (array_keys(self::LOCALES) as $locale) {
                $slug = $translations[$locale]['slug'] ?? null;

                if (blank($slug)) {
                    continue;
                }

                $duplicate = app('db')
                    ->table($translationTable)
                    ->where('locale', $locale)
                    ->where('slug', $slug)
                    ->when($record?->exists, fn ($query) => $query->where($foreignKey, '!=', $record->getKey()))
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        "data.translations.{$locale}.slug" => 'Slug už existuje pre tento jazyk.',
                    ]);
                }
            }
        }

        if ($rules !== []) {
            Validator::make(['translations' => $translations], $rules)->validate();
        }
    }

    /**
     * @param  array<int, string>  $fields
     */
    public static function tabs(array $fields): Tabs
    {
        return Tabs::make('Preklady')
            ->tabs(
                collect(self::LOCALES)
                    ->map(fn (string $label, string $locale): Tab => Tab::make($label)
                        ->badge(fn (?Model $record): string => self::translationBadge($record, $locale, $fields))
                        ->schema(self::schemaForLocale($locale, $fields)))
                    ->values()
                    ->all(),
            )
            ->columnSpanFull();
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<int, mixed>
     */
    protected static function schemaForLocale(string $locale, array $fields): array
    {
        $schema = [];

        foreach ($fields as $field) {
            $name = "translations.{$locale}.{$field}";

            $schema[] = match ($field) {
                'slug' => TextInput::make($name)
                    ->label('Slug')
                    ->maxLength(255),
                'title' => TextInput::make($name)
                    ->label('Názov')
                    ->maxLength(255),
                'name' => TextInput::make($name)
                    ->label('Názov')
                    ->maxLength(255),
                'short_title' => TextInput::make($name)
                    ->label('Krátky názov')
                    ->maxLength(255),
                'excerpt' => Textarea::make($name)
                    ->label('Perex')
                    ->rows(3)
                    ->columnSpanFull(),
                'description' => Textarea::make($name)
                    ->label('Popis')
                    ->rows(4)
                    ->columnSpanFull(),
                'body' => RichEditor::make($name)
                    ->label('Obsah')
                    ->columnSpanFull(),
                'seo_title' => TextInput::make($name)
                    ->label('SEO názov')
                    ->maxLength(255),
                'seo_description' => Textarea::make($name)
                    ->label('SEO popis')
                    ->rows(3)
                    ->columnSpanFull(),
                'og_title' => TextInput::make($name)
                    ->label('OG názov')
                    ->maxLength(255),
                'og_description' => Textarea::make($name)
                    ->label('OG popis')
                    ->rows(3)
                    ->columnSpanFull(),
                'canonical_url' => TextInput::make($name)
                    ->label('Kanonická URL')
                    ->url()
                    ->maxLength(255),
                default => TextInput::make($name)->label($field),
            };
        }

        return $schema;
    }

    /**
     * @param  array<int, string>  $fields
     */
    protected static function translationBadge(?Model $record, string $locale, array $fields): string
    {
        if (! $record?->exists) {
            return $locale === 'sk' ? 'povinné' : 'chýba';
        }

        $translation = $record->translations->firstWhere('locale', $locale);

        if (! $translation) {
            return $locale === 'sk' ? 'povinné' : 'chýba';
        }

        $requiredFields = array_values(array_intersect($fields, ['title', 'name', 'slug']));

        foreach ($requiredFields as $field) {
            if (blank($translation->{$field})) {
                return 'chýba';
            }
        }

        return 'OK';
    }

    protected static function hasMeaningfulValue(mixed $value): bool
    {
        if (! filled($value)) {
            return false;
        }

        if (! is_string($value)) {
            return true;
        }

        return ! in_array(trim($value), ['', '<p></p>'], true);
    }
}
