<?php

namespace App\Support;

use Illuminate\Http\Request;

class Locale
{
    public const COOKIE_NAME = 'preferred_locale';

    public static function locales(): array
    {
        return config('site.locales', []);
    }

    public static function fallback(): string
    {
        return config('site.fallback_locale', 'sk');
    }

    public static function prefix(string $locale): string
    {
        return config("site.public_prefixes.{$locale}", $locale);
    }

    /**
     * @return array<string, string>
     */
    public static function prefixes(): array
    {
        return config('site.public_prefixes', []);
    }

    public static function fromPrefix(?string $prefix): string
    {
        if (! self::validPrefix($prefix)) {
            return self::fallback();
        }

        $locale = array_search($prefix, self::prefixes(), true);

        return is_string($locale) ? $locale : self::fallback();
    }

    public static function supported(?string $locale): bool
    {
        return array_key_exists((string) $locale, self::locales());
    }

    public static function validPrefix(?string $prefix): bool
    {
        return in_array((string) $prefix, array_values(self::prefixes()), true);
    }

    public static function localeFromRequest(Request $request): string
    {
        $cookie = $request->cookie(self::COOKIE_NAME);

        if (self::supported($cookie)) {
            return $cookie;
        }

        $country = strtoupper((string) $request->header('cf-ipcountry'));

        if (isset(config('site.country_locale_map')[$country])) {
            return config('site.country_locale_map')[$country];
        }

        if (! $request->headers->has('Accept-Language') || blank($request->headers->get('Accept-Language'))) {
            return self::fallback();
        }

        $accepted = $request->getPreferredLanguage(array_keys(self::locales()));

        if (self::supported($accepted)) {
            return $accepted;
        }

        return self::fallback();
    }

    public static function url(string $locale, ?string $key = null, ?string $slug = null): string
    {
        $prefix = self::prefix($locale);

        if ($key === null || $key === 'home') {
            return url('/'.$prefix);
        }

        $path = trim((string) config("site.paths.{$locale}.{$key}", ''), '/');

        if ($path === '') {
            return url('/'.$prefix);
        }

        return url('/'.$prefix.'/'.$path.($slug ? '/'.trim($slug, '/') : ''));
    }

    public static function path(string $locale, string $key): string
    {
        return '/'.self::prefix($locale).'/'.trim((string) data_get(config("site.paths.{$locale}", []), $key, ''), '/');
    }

    public static function routeFor(string $locale, string $key, array $parameters = []): string
    {
        $base = self::path($locale, $key);

        if ($parameters === []) {
            return $base;
        }

        return route($parameters['_route'] ?? 'home', array_filter($parameters, fn ($value, $name) => $name !== '_route', ARRAY_FILTER_USE_BOTH), false) ?: $base;
    }
}
