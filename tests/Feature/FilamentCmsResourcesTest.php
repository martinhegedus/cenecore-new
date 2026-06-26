<?php

namespace Tests\Feature;

use App\Filament\Resources\PageResource;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\ProjectCategoryResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\TechnologyResource;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentCmsResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_user_cannot_access_cms_resources(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        foreach ($this->resourceIndexUrls() as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    public function test_admin_user_can_access_cms_resource_indexes(): void
    {
        $admin = $this->adminUser();

        foreach ($this->resourceIndexUrls() as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_admin_can_create_page_with_slovak_translation(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreatePage::class)
            ->fillForm([
                'type' => 'standard',
                'status' => 'published',
                'is_indexable' => true,
                'published_at' => now(),
                'translations' => [
                    'sk' => [
                        'slug' => 'uvod',
                        'title' => 'Úvod',
                        'excerpt' => 'Krátky popis',
                        'body' => '<p>Obsah stránky</p>',
                        'seo_title' => 'SEO Úvod',
                        'seo_description' => 'SEO popis',
                        'og_title' => 'OG Úvod',
                        'og_description' => 'OG popis',
                        'canonical_url' => 'https://example.com/uvod',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = Page::query()->firstOrFail();

        $this->assertSame($admin->id, $page->created_by);
        $this->assertSame($admin->id, $page->updated_by);
        $this->assertDatabaseHas('page_translations', [
            'page_id' => $page->id,
            'locale' => 'sk',
            'slug' => 'uvod',
            'title' => 'Úvod',
        ]);
    }

    public function test_admin_can_create_service_with_all_translations(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(CreateService::class)
            ->fillForm([
                'icon' => 'code',
                'sort_order' => 10,
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now(),
                'translations' => [
                    'sk' => [
                        'slug' => 'vyvoj-webov',
                        'title' => 'Vývoj webov',
                        'short_title' => 'Weby',
                        'excerpt' => 'Tvorba webov',
                        'body' => '<p>Obsah</p>',
                    ],
                    'cs' => [
                        'slug' => 'vyvoj-webu',
                        'title' => 'Vývoj webů',
                        'short_title' => 'Weby',
                        'excerpt' => 'Tvorba webů',
                        'body' => '<p>Obsah</p>',
                    ],
                    'en' => [
                        'slug' => 'web-development',
                        'title' => 'Web development',
                        'short_title' => 'Web',
                        'excerpt' => 'Web delivery',
                        'body' => '<p>Content</p>',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $service = Service::query()->firstOrFail();

        $this->assertSame(['cs', 'en', 'sk'], $service->translations()->pluck('locale')->sort()->values()->all());
    }

    public function test_translation_records_are_updated_instead_of_duplicated(): void
    {
        $service = Service::query()->create([
            'status' => 'draft',
        ]);

        foreach (['sk', 'cs', 'en'] as $locale) {
            $service->translations()->create([
                'locale' => $locale,
                'slug' => "service-{$locale}",
                'title' => "Service {$locale}",
            ]);
        }

        Livewire::actingAs($this->adminUser())
            ->test(EditService::class, ['record' => $service->getKey()])
            ->fillForm([
                'icon' => 'updated',
                'sort_order' => 5,
                'is_featured' => false,
                'status' => 'draft',
                'published_at' => null,
                'translations' => [
                    'sk' => [
                        'slug' => 'sluzba',
                        'title' => 'Aktualizovaná služba',
                    ],
                    'cs' => [
                        'slug' => 'sluzba-cs',
                        'title' => 'Aktualizovaná služba CS',
                    ],
                    'en' => [
                        'slug' => 'service',
                        'title' => 'Updated service',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(3, $service->translations()->count());
        $this->assertDatabaseHas('service_translations', [
            'service_id' => $service->id,
            'locale' => 'sk',
            'slug' => 'sluzba',
            'title' => 'Aktualizovaná služba',
        ]);
    }

    public function test_locale_specific_slug_uniqueness_is_enforced(): void
    {
        PageTranslation::query()->create([
            'page_id' => Page::query()->create()->id,
            'locale' => 'sk',
            'slug' => 'kontakt',
            'title' => 'Kontakt',
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(CreatePage::class)
            ->fillForm([
                'status' => 'published',
                'translations' => [
                    'sk' => [
                        'slug' => 'kontakt',
                        'title' => 'Iný kontakt',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors(['translations.sk.slug']);
    }

    public function test_page_updated_by_changes_on_edit(): void
    {
        $creator = $this->adminUser();
        $editor = User::factory()->create([
            'is_admin' => true,
        ]);

        $page = Page::query()->create([
            'status' => 'draft',
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);
        $page->translations()->create([
            'locale' => 'sk',
            'slug' => 'povodna',
            'title' => 'Pôvodná',
        ]);

        Livewire::actingAs($editor)
            ->test(EditPage::class, ['record' => $page->getKey()])
            ->fillForm([
                'type' => 'standard',
                'status' => 'draft',
                'is_indexable' => true,
                'published_at' => null,
                'translations' => [
                    'sk' => [
                        'slug' => 'nova',
                        'title' => 'Nová',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page->refresh();

        $this->assertSame($creator->id, $page->created_by);
        $this->assertSame($editor->id, $page->updated_by);
    }

    /**
     * @return array<int, string>
     */
    protected function resourceIndexUrls(): array
    {
        return [
            PageResource::getUrl('index'),
            ServiceResource::getUrl('index'),
            TechnologyResource::getUrl('index'),
            ProjectCategoryResource::getUrl('index'),
        ];
    }

    protected function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }
}
