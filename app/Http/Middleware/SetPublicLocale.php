<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        $resolved = $locale ? Locale::fromPrefix($locale) : Locale::localeFromRequest($request);

        app()->setLocale($resolved);
        $request->attributes->set('public_locale', $resolved);

        return $next($request);
    }
}
