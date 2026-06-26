<?php

namespace Tests\Feature;

use App\Support\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_slovak_by_default(): void
    {
        $this
            ->withCookie(Locale::COOKIE_NAME, 'invalid')
            ->withHeader('Accept-Language', '')
            ->get('/')
            ->assertRedirect('/sk');
    }

    public function test_valid_locale_cookie_overrides_other_detection(): void
    {
        $this
            ->withCookie(Locale::COOKIE_NAME, 'en')
            ->withHeader('CF-IPCountry', 'SK')
            ->get('/')
            ->assertRedirect('/en');
    }

    public function test_cloudflare_slovakia_country_redirects_to_slovak(): void
    {
        $this
            ->withHeader('CF-IPCountry', 'SK')
            ->get('/')
            ->assertRedirect('/sk');
    }

    public function test_cloudflare_czech_country_redirects_to_czech_prefix(): void
    {
        $this
            ->withHeader('CF-IPCountry', 'CZ')
            ->get('/')
            ->assertRedirect('/cz');
    }

    public function test_accept_language_czech_redirects_to_czech_prefix(): void
    {
        $this
            ->withHeader('Accept-Language', 'cs-CZ,cs;q=0.9,en;q=0.8')
            ->get('/')
            ->assertRedirect('/cz');
    }

    public function test_unsupported_prefix_returns_not_found(): void
    {
        $this->get('/de')->assertNotFound();
    }

    public function test_localized_homepages_render(): void
    {
        $this->get('/sk')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('locale', 'sk')
            ->where('publicLocalePrefix', 'sk'));

        $this->get('/cz')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('locale', 'cs')
            ->where('publicLocalePrefix', 'cz'));

        $this->get('/en')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('locale', 'en')
            ->where('publicLocalePrefix', 'en'));
    }

    public function test_representative_static_localized_paths_render(): void
    {
        foreach ([
            '/sk/o-nas',
            '/sk/sluzby',
            '/sk/projekty',
            '/sk/blog',
            '/sk/kontakt',
            '/sk/nezavazna-ponuka',
            '/sk/ochrana-osobnych-udajov',
            '/sk/cookies',
            '/cz/o-nas',
            '/cz/sluzby',
            '/cz/projekty',
            '/cz/blog',
            '/cz/kontakt',
            '/cz/nezavazna-nabidka',
            '/cz/ochrana-osobnich-udaju',
            '/cz/cookies',
            '/en/about',
            '/en/services',
            '/en/projects',
            '/en/insights',
            '/en/contact',
            '/en/request-a-quote',
            '/en/privacy-policy',
            '/en/cookies',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_language_switcher_props_contain_equivalent_static_urls(): void
    {
        $this->get('/sk/o-nas')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Public/Page')
            ->where('localizedUrls.sk', url('/sk/o-nas'))
            ->where('localizedUrls.cs', url('/cz/o-nas'))
            ->where('localizedUrls.en', url('/en/about'))
            ->has('supportedLocales', 3)
            ->where('supportedLocales.1.code', 'cs')
            ->where('supportedLocales.1.prefix', 'cz')
            ->where('supportedLocales.1.url', url('/cz/o-nas')));
    }

    public function test_sitemap_endpoint_works(): void
    {
        $this
            ->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('content-type', 'application/xml')
            ->assertSee('<urlset', false)
            ->assertDontSee('/admin', false)
            ->assertDontSee('/login', false);
    }

    public function test_robots_endpoint_works(): void
    {
        $this
            ->get('/robots.txt')
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('User-agent: *')
            ->assertSee('Sitemap:');
    }

    public function test_missing_public_route_renders_inertia_404_response(): void
    {
        $this->get('/sk/missing-page')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/NotFound')
                ->where('seo.robots', 'noindex,nofollow'));
    }

    public function test_canonical_and_hreflang_props_use_correct_locale_mapping(): void
    {
        $this->get('/cz/kontakt')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Public/Page')
            ->where('locale', 'cs')
            ->where('seo.canonical', url('/cz/kontakt'))
            ->where('localizedUrls.sk', url('/sk/kontakt'))
            ->where('localizedUrls.cs', url('/cz/kontakt'))
            ->where('localizedUrls.en', url('/en/contact')));
    }
}
