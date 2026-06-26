<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PublicController extends Controller
{
    public function home(Request $request): InertiaResponse
    {
        $locale = app()->getLocale();

        return Inertia::render('Public/Home', $this->basePageData($request, 'home', [
            'hero' => [
                'eyebrow' => 'CENECORE',
                'headline' => [
                    'sk' => 'Digitálne systémy, ktoré posúvajú biznis vpred.',
                    'cs' => 'Digitální systémy, které posouvají byznys vpřed.',
                    'en' => 'Digital systems that move business forward.',
                ][$locale],
                'lead' => [
                    'sk' => 'Staviame rýchle weby, interné systémy, ecommerce riešenia a integrácie s dôrazom na výkon, SEO a dlhodobú udržateľnosť.',
                    'cs' => 'Tvoříme rychlé weby, interní systémy, ecommerce řešení a integrace s důrazem na výkon, SEO a dlouhodobou udržitelnost.',
                    'en' => 'We build fast web products, internal systems, ecommerce platforms, and integrations with a focus on performance, SEO, and long-term maintainability.',
                ][$locale],
            ],
            'services' => $this->servicesData($locale),
            'featuredProjects' => $this->projectsData($locale, true),
            'technologies' => $this->technologiesData($locale),
            'testimonials' => $this->testimonialsData($locale),
            'articles' => $this->blogPostsData($locale),
            'faq' => $this->faqData($locale),
            'cta' => [
                'quoteUrl' => Locale::url($locale, 'quote'),
                'projectsUrl' => Locale::url($locale, 'projects'),
                'quoteLabel' => [
                    'sk' => 'Začať projekt',
                    'cs' => 'Začít projekt',
                    'en' => 'Start your project',
                ][$locale],
                'projectsLabel' => [
                    'sk' => 'Pozrieť projekty',
                    'cs' => 'Prohlédnout projekty',
                    'en' => 'Explore our work',
                ][$locale],
            ],
        ], [
            'title' => [
                'sk' => 'CENECORE - digitálne systémy pre rast',
                'cs' => 'CENECORE - digitální systémy pro růst',
                'en' => 'CENECORE - digital systems for growth',
            ][$locale],
            'description' => [
                'sk' => 'Prémiový Laravel a Vue štúdio pre weby, interné systémy, ecommerce, SEO a integrácie.',
                'cs' => 'Prémiové Laravel a Vue studio pro weby, interní systémy, ecommerce, SEO a integrace.',
                'en' => 'A premium Laravel and Vue studio for websites, internal systems, ecommerce, SEO, and integrations.',
            ][$locale],
        ]));
    }

    public function about(Request $request): InertiaResponse
    {
        return $this->genericPage($request, 'about', [
            'title' => [
                'sk' => 'O nás',
                'cs' => 'O nás',
                'en' => 'About',
            ][app()->getLocale()],
            'headline' => [
                'sk' => 'Nie sme agentúra na šablóny. Staviame technické produkty.',
                'cs' => 'Nejsme šablonová agentura. Stavíme technické produkty.',
                'en' => 'We are not a template agency. We build technical products.',
            ][app()->getLocale()],
            'content' => [
                [
                    'sk' => 'Pracujeme s Laravelom, Vue, Inertia a Filamentom na produktoch, ktoré potrebujú byť rýchle, udržateľné a obchodne použiteľné.',
                    'cs' => 'Pracujeme s Laravel, Vue, Inertia a Filamentem na produktech, které musí být rychlé, udržitelné a obchodně použitelné.',
                    'en' => 'We work with Laravel, Vue, Inertia, and Filament on products that need to be fast, maintainable, and commercially useful.',
                ][app()->getLocale()],
            ],
        ]);
    }

    public function servicesIndex(Request $request): InertiaResponse
    {
        return Inertia::render('Public/Services/Index', $this->basePageData($request, 'services.index', [
            'services' => $this->servicesData(app()->getLocale()),
            'featuredProjects' => $this->projectsData(app()->getLocale(), true),
        ], ['title' => $this->localizedStaticTitle('services')]));
    }

    public function serviceShow(Request $request, string $slug): InertiaResponse|RedirectResponse
    {
        $service = $this->resolveService($slug);

        if (! $service) {
            return redirect()->route('public.'.app()->getLocale().'.services.index');
        }

        return Inertia::render('Public/Services/Show', $this->basePageData($request, 'services.show', [
            'service' => $this->serviceData($service, app()->getLocale()),
            'relatedProjects' => $this->projectsData(app()->getLocale()),
            'faq' => $this->faqData(app()->getLocale(), $service->id),
        ], [
            'title' => $service->translation(app()->getLocale())->first()?->title ?? 'Service',
        ]));
    }

    public function projectsIndex(Request $request): InertiaResponse
    {
        return Inertia::render('Public/Projects/Index', $this->basePageData($request, 'projects.index', [
            'projects' => $this->projectsData(app()->getLocale()),
            'featuredProject' => $this->projectsData(app()->getLocale(), true)->first(),
        ], ['title' => $this->localizedStaticTitle('projects')]));
    }

    public function projectShow(Request $request, string $slug): InertiaResponse|RedirectResponse
    {
        $project = $this->resolveProject($slug);

        if (! $project) {
            return redirect()->route('public.'.app()->getLocale().'.projects.index');
        }

        return Inertia::render('Public/Projects/Show', $this->basePageData($request, 'projects.show', [
            'project' => $this->projectData($project, app()->getLocale()),
            'relatedProjects' => $this->projectsData(app()->getLocale())
                ->filter(fn (array $item) => $item['id'] !== $project->id)
                ->take(3)
                ->values(),
        ], [
            'title' => $project->translation(app()->getLocale())->first()?->title ?? 'Project',
        ]));
    }

    public function blogIndex(Request $request): InertiaResponse
    {
        return Inertia::render('Public/Blog/Index', $this->basePageData($request, 'blog.index', [
            'posts' => $this->blogPostsData(app()->getLocale()),
        ], ['title' => $this->localizedStaticTitle('blog')]));
    }

    public function blogShow(Request $request, string $slug): InertiaResponse|RedirectResponse
    {
        $post = $this->resolveBlogPost($slug);

        if (! $post) {
            return redirect()->route('public.'.app()->getLocale().'.blog.index');
        }

        return Inertia::render('Public/Blog/Show', $this->basePageData($request, 'blog.show', [
            'post' => $this->blogPostData($post, app()->getLocale()),
            'relatedPosts' => $this->blogPostsData(app()->getLocale())
                ->filter(fn (array $item) => $item['id'] !== $post->id)
                ->take(3)
                ->values(),
        ], [
            'title' => $post->translation(app()->getLocale())->first()?->title ?? 'Article',
        ]));
    }

    public function contact(Request $request): InertiaResponse
    {
        return $this->genericPage($request, 'contact', [
            'title' => [
                'sk' => 'Kontakt',
                'cs' => 'Kontakt',
                'en' => 'Contact',
            ][app()->getLocale()],
            'headline' => [
                'sk' => 'Ozvite sa. Zistíme, čo má skutočný zmysel.',
                'cs' => 'Ozvěte se. Zjistíme, co má skutečný smysl.',
                'en' => 'Get in touch. We will define what actually matters.',
            ][app()->getLocale()],
        ]);
    }

    public function quote(Request $request): InertiaResponse
    {
        return $this->genericPage($request, 'quote', [
            'title' => [
                'sk' => 'Nezáväzná ponuka',
                'cs' => 'Nezávazná nabídka',
                'en' => 'Request a quote',
            ][app()->getLocale()],
            'headline' => [
                'sk' => 'Nezáväzná ponuka v niekoľkých krokoch.',
                'cs' => 'Nezávazná nabídka v několika krocích.',
                'en' => 'A quote request in a few focused steps.',
            ][app()->getLocale()],
        ]);
    }

    public function thankYou(Request $request): InertiaResponse
    {
        return $this->genericPage($request, 'thank-you', [
            'title' => [
                'sk' => 'Ďakujeme',
                'cs' => 'Děkujeme',
                'en' => 'Thank you',
            ][app()->getLocale()],
            'headline' => [
                'sk' => 'Ďakujeme. Ozveme sa čo najskôr.',
                'cs' => 'Děkujeme. Ozveme se co nejdříve.',
                'en' => 'Thank you. We will follow up shortly.',
            ][app()->getLocale()],
        ]);
    }

    public function privacy(Request $request): InertiaResponse
    {
        return $this->genericPage($request, 'privacy', [
            'title' => [
                'sk' => 'Ochrana osobných údajov',
                'cs' => 'Ochrana osobních údajů',
                'en' => 'Privacy policy',
            ][app()->getLocale()],
            'headline' => [
                'sk' => 'Ochrana osobných údajov',
                'cs' => 'Ochrana osobních údajů',
                'en' => 'Privacy policy',
            ][app()->getLocale()],
        ]);
    }

    public function cookies(Request $request): InertiaResponse
    {
        return $this->genericPage($request, 'cookies', [
            'title' => [
                'sk' => 'Cookies',
                'cs' => 'Cookies',
                'en' => 'Cookies',
            ][app()->getLocale()],
            'headline' => [
                'sk' => 'Cookies a preferencie',
                'cs' => 'Cookies a předvolby',
                'en' => 'Cookies and preferences',
            ][app()->getLocale()],
        ]);
    }

    public function sitemap(): \Illuminate\Http\Response
    {
        $localePaths = config('site.paths');

        $urls = collect(config('site.public_prefixes'))
            ->flatMap(function (string $prefix, string $locale) use ($localePaths): array {
                $base = url('/'.$prefix);

                return array_filter([
                    $base,
                    $base.'/'.$localePaths[$locale]['about'],
                    $base.'/'.$localePaths[$locale]['services'],
                    $base.'/'.$localePaths[$locale]['projects'],
                    $base.'/'.$localePaths[$locale]['blog'],
                    $base.'/'.$localePaths[$locale]['contact'],
                    $base.'/'.$localePaths[$locale]['quote'],
                    $base.'/'.$localePaths[$locale]['privacy'],
                    $base.'/'.$localePaths[$locale]['cookies'],
                ]);
            })
            ->values();

        $xml = view('sitemap', ['urls' => $urls])->render();

        return Response::make($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): \Illuminate\Http\Response
    {
        $content = "User-agent: *\nAllow: /\nSitemap: ".route('sitemap')."\n";

        return Response::make($content, 200)->header('Content-Type', 'text/plain');
    }

    protected function genericPage(Request $request, string $component, array $props): InertiaResponse
    {
        $content = $props['content'] ?? [];

        unset($props['content']);

        return Inertia::render('Public/Page', $this->basePageData($request, $component, array_merge($props, [
            'content' => $content,
        ]), [
            'title' => $props['title'] ?? config('app.name'),
        ]));
    }

    protected function basePageData(Request $request, string $page, array $props = [], array $seo = []): array
    {
        $locale = app()->getLocale();

        return array_merge($props, [
            'locale' => $locale,
            'publicLocalePrefix' => Locale::prefix($locale),
            'supportedLocales' => $this->supportedLocales($page, $request),
            'seo' => array_merge([
                'title' => config('app.name'),
                'description' => $this->defaultDescription($locale),
                'canonical' => $request->url(),
                'robots' => 'index,follow',
                'ogTitle' => null,
                'ogDescription' => null,
                'ogType' => 'website',
            ], $seo),
            'navigation' => $this->navigationLinks($locale),
            'site' => [
                'name' => 'CENECORE',
                'contact' => config('site.contact'),
            ],
            'localizedUrls' => $this->localizedUrls($page, $request),
        ]);
    }

    protected function navigationLinks(string $locale): array
    {
        $prefix = Locale::prefix($locale);
        $paths = config("site.paths.{$locale}");

        return [
            [
                'label' => ['sk' => 'Služby', 'cs' => 'Služby', 'en' => 'Services'][$locale],
                'url' => "/{$prefix}/{$paths['services']}",
            ],
            [
                'label' => ['sk' => 'Projekty', 'cs' => 'Projekty', 'en' => 'Projects'][$locale],
                'url' => "/{$prefix}/{$paths['projects']}",
            ],
            [
                'label' => ['sk' => 'Blog', 'cs' => 'Blog', 'en' => 'Insights'][$locale],
                'url' => "/{$prefix}/{$paths['blog']}",
            ],
            [
                'label' => ['sk' => 'Kontakt', 'cs' => 'Kontakt', 'en' => 'Contact'][$locale],
                'url' => "/{$prefix}/{$paths['contact']}",
            ],
        ];
    }

    protected function supportedLocales(string $page, Request $request): array
    {
        $urls = $this->localizedUrls($page, $request);

        return collect(Locale::locales())
            ->map(fn (string $label, string $code): array => [
                'code' => $code,
                'label' => $label,
                'prefix' => Locale::prefix($code),
                'url' => $urls[$code] ?? Locale::url($code),
                'switchUrl' => route('locale.switch', [
                    'locale' => Locale::prefix($code),
                    'redirect' => $urls[$code] ?? Locale::url($code),
                ]),
            ])
            ->values()
            ->all();
    }

    protected function localizedUrls(string $page, Request $request): array
    {
        $staticKey = match ($page) {
            'home' => 'home',
            'about' => 'about',
            'services.index' => 'services',
            'projects.index' => 'projects',
            'blog.index' => 'blog',
            'contact' => 'contact',
            'quote' => 'quote',
            'privacy' => 'privacy',
            'cookies' => 'cookies',
            default => null,
        };

        return collect(Locale::prefixes())
            ->mapWithKeys(fn (string $prefix, string $targetLocale): array => [
                $targetLocale => $staticKey ? Locale::url($targetLocale, $staticKey) : $this->fallbackLocalizedUrl($targetLocale, $page),
            ])
            ->all();
    }

    protected function fallbackLocalizedUrl(string $locale, string $page): string
    {
        return match ($page) {
            'services.show' => Locale::url($locale, 'services'),
            'projects.show' => Locale::url($locale, 'projects'),
            'blog.show' => Locale::url($locale, 'blog'),
            default => Locale::url($locale),
        };
    }

    protected function localizedStaticTitle(string $key): string
    {
        return [
            'services' => ['sk' => 'Služby', 'cs' => 'Služby', 'en' => 'Services'],
            'projects' => ['sk' => 'Projekty', 'cs' => 'Projekty', 'en' => 'Projects'],
            'blog' => ['sk' => 'Blog', 'cs' => 'Blog', 'en' => 'Insights'],
        ][$key][app()->getLocale()];
    }

    protected function defaultDescription(string $locale): string
    {
        return [
            'sk' => 'CENECORE navrhuje a stavia rýchle weby, interné systémy, ecommerce a integrácie.',
            'cs' => 'CENECORE navrhuje a staví rychlé weby, interní systémy, ecommerce a integrace.',
            'en' => 'CENECORE designs and builds fast websites, internal systems, ecommerce, and integrations.',
        ][$locale];
    }

    protected function servicesData(string $locale): array
    {
        return Service::query()
            ->published()
            ->ordered()
            ->with('translations')
            ->get()
            ->map(fn (Service $service): array => $this->serviceData($service, $locale))
            ->all();
    }

    protected function serviceData(Service $service, string $locale): array
    {
        $translation = $service->translation($locale)->first() ?? $service->translation(Locale::fallback())->first();

        return [
            'id' => $service->id,
            'title' => $translation?->title,
            'short_title' => $translation?->short_title,
            'excerpt' => $translation?->excerpt,
            'slug' => $translation?->slug,
            'url' => $translation ? '/'.Locale::prefix($locale).'/'.config("site.paths.{$locale}.services").'/'.$translation->slug : null,
        ];
    }

    protected function projectsData(string $locale, bool $featuredOnly = false): Collection
    {
        return Project::query()
            ->published()
            ->when($featuredOnly, fn ($query) => $query->featured())
            ->ordered()
            ->with('translations')
            ->get()
            ->map(fn (Project $project): array => $this->projectData($project, $locale));
    }

    protected function projectData(Project $project, string $locale): array
    {
        $translation = $project->translation($locale)->first() ?? $project->translation(Locale::fallback())->first();

        return [
            'id' => $project->id,
            'title' => $translation?->title,
            'summary' => $translation?->summary,
            'slug' => $translation?->slug,
            'client_name' => $project->client_name,
            'url' => $translation ? '/'.Locale::prefix($locale).'/'.config("site.paths.{$locale}.projects").'/'.$translation->slug : null,
        ];
    }

    protected function technologiesData(string $locale): array
    {
        return Technology::query()
            ->active()
            ->ordered()
            ->with('translations')
            ->get()
            ->map(fn (Technology $technology): array => [
                'id' => $technology->id,
                'name' => $technology->translation($locale)->first()?->name,
                'description' => $technology->translation($locale)->first()?->description,
            ])
            ->all();
    }

    protected function testimonialsData(string $locale): array
    {
        return Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with('translations')
            ->get()
            ->map(fn (Testimonial $testimonial): array => [
                'id' => $testimonial->id,
                'client_name' => $testimonial->client_name,
                'quote' => $testimonial->translation($locale)->first()?->quote,
                'rating' => $testimonial->rating,
                'project' => $testimonial->project?->translation($locale)->first()?->title,
            ])
            ->all();
    }

    protected function blogPostsData(string $locale): array
    {
        return BlogPost::query()
            ->published()
            ->latest('published_at')
            ->with('translations')
            ->get()
            ->map(fn (BlogPost $post): array => $this->blogPostData($post, $locale))
            ->all();
    }

    protected function blogPostData(BlogPost $post, string $locale): array
    {
        $translation = $post->translation($locale)->first() ?? $post->translation(Locale::fallback())->first();

        return [
            'id' => $post->id,
            'title' => $translation?->title,
            'excerpt' => $translation?->excerpt,
            'slug' => $translation?->slug,
            'url' => $translation ? '/'.Locale::prefix($locale).'/'.config("site.paths.{$locale}.blog").'/'.$translation->slug : null,
        ];
    }

    protected function faqData(string $locale, ?int $serviceId = null): array
    {
        return Faq::query()
            ->where('is_active', true)
            ->when($serviceId, fn ($query) => $query->where('service_id', $serviceId))
            ->orderBy('sort_order')
            ->with('translations')
            ->get()
            ->map(fn ($faq): array => [
                'id' => $faq->id,
                'question' => $faq->translation($locale)->first()?->question,
                'answer' => $faq->translation($locale)->first()?->answer,
            ])
            ->all();
    }

    protected function resolveService(string $slug): ?Service
    {
        return Service::query()
            ->with('translations')
            ->where(function ($query) use ($slug): void {
                $query->whereHas('translations', fn ($query) => $query->where('locale', app()->getLocale())->where('slug', $slug))
                    ->orWhereHas('translations', fn ($query) => $query->where('locale', Locale::fallback())->where('slug', $slug));
            })
            ->first();
    }

    protected function resolveProject(string $slug): ?Project
    {
        return Project::query()
            ->with('translations')
            ->where(function ($query) use ($slug): void {
                $query->whereHas('translations', fn ($query) => $query->where('locale', app()->getLocale())->where('slug', $slug))
                    ->orWhereHas('translations', fn ($query) => $query->where('locale', Locale::fallback())->where('slug', $slug));
            })
            ->first();
    }

    protected function resolveBlogPost(string $slug): ?BlogPost
    {
        return BlogPost::query()
            ->with('translations')
            ->where(function ($query) use ($slug): void {
                $query->whereHas('translations', fn ($query) => $query->where('locale', app()->getLocale())->where('slug', $slug))
                    ->orWhereHas('translations', fn ($query) => $query->where('locale', Locale::fallback())->where('slug', $slug));
            })
            ->first();
    }
}
