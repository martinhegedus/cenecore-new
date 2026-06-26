<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (Request $request) {
    $locale = Locale::localeFromRequest($request);

    return redirect()->to('/'.Locale::prefix($locale), 302);
});

Route::get('/locale/{locale}', function (Request $request, string $locale) {
    abort_unless(Locale::supported($locale) || Locale::validPrefix($locale), 404);

    $resolvedLocale = Locale::supported($locale) ? $locale : Locale::fromPrefix($locale);
    $target = $request->query('redirect', Locale::url($resolvedLocale));

    if (! str_starts_with((string) $target, url('/'))) {
        $target = Locale::url($resolvedLocale);
    }

    return redirect()->to($target)->withCookie(cookie(Locale::COOKIE_NAME, $resolvedLocale, 60 * 24 * 365));
})->name('locale.switch');

foreach (Locale::prefixes() as $locale => $prefix) {
    Route::prefix($prefix)
        ->middleware(['web', "public-locale:{$prefix}"])
        ->as("public.{$locale}.")
        ->group(function () use ($locale) {
            Route::get('/', [PublicController::class, 'home'])->name('home');

            Route::get(config("site.paths.{$locale}.about"), [PublicController::class, 'about'])->name('about');
            Route::get(config("site.paths.{$locale}.services"), [PublicController::class, 'servicesIndex'])->name('services.index');
            Route::get(config("site.paths.{$locale}.services").'/{slug}', [PublicController::class, 'serviceShow'])->name('services.show');
            Route::get(config("site.paths.{$locale}.projects"), [PublicController::class, 'projectsIndex'])->name('projects.index');
            Route::get(config("site.paths.{$locale}.projects").'/{slug}', [PublicController::class, 'projectShow'])->name('projects.show');
            Route::get(config("site.paths.{$locale}.blog"), [PublicController::class, 'blogIndex'])->name('blog.index');
            Route::get(config("site.paths.{$locale}.blog").'/{slug}', [PublicController::class, 'blogShow'])->name('blog.show');
            Route::get(config("site.paths.{$locale}.contact"), [PublicController::class, 'contact'])->name('contact');
            Route::get(config("site.paths.{$locale}.quote"), [PublicController::class, 'quote'])->name('quote');
            Route::get('thank-you', [PublicController::class, 'thankYou'])->name('thank-you');
            Route::get(config("site.paths.{$locale}.privacy"), [PublicController::class, 'privacy'])->name('privacy');
            Route::get(config("site.paths.{$locale}.cookies"), [PublicController::class, 'cookies'])->name('cookies');
        });
}

Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PublicController::class, 'robots'])->name('robots');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['verified'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
