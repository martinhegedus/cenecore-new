<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetPublicLocale;
use App\Support\Locale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'public-locale' => SetPublicLocale::class,
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return Inertia::render('Public/NotFound', [
                'seo' => [
                    'title' => '404',
                    'description' => 'Page not found',
                    'canonical' => $request->url(),
                    'robots' => 'noindex,nofollow',
                ],
                'locale' => app()->getLocale(),
                'publicLocalePrefix' => Locale::prefix(app()->getLocale()),
                'supportedLocales' => collect(Locale::locales())
                    ->map(fn (string $label, string $code): array => [
                        'code' => $code,
                        'label' => $label,
                        'prefix' => Locale::prefix($code),
                        'url' => Locale::url($code),
                        'switchUrl' => route('locale.switch', [
                            'locale' => Locale::prefix($code),
                            'redirect' => Locale::url($code),
                        ]),
                    ])
                    ->values()
                    ->all(),
                'localizedUrls' => collect(Locale::prefixes())
                    ->mapWithKeys(fn (string $prefix, string $code): array => [$code => Locale::url($code)])
                    ->all(),
                'navigation' => [],
            ])->toResponse($request)->setStatusCode(404);
        });
    })->create();
