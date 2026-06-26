<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CmsMediaFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_upload_and_retrieve_cover_media(): void
    {
        Storage::fake('public');

        $service = Service::query()->create();

        $service
            ->addMedia($this->pngUpload('cover.png'))
            ->withCustomProperties([
                'alt' => ['sk' => 'Titulný obrázok služby'],
                'title' => 'Cover',
            ])
            ->toMediaCollection('cover');

        $media = $service->getFirstMedia('cover');

        $this->assertNotNull($media);
        $this->assertSame('cover', $media->collection_name);
        $this->assertSame('image/png', $media->mime_type);
        $this->assertSame('Titulný obrázok služby', $service->localizedMediaAlt('cover', 'sk'));
        $this->assertNotNull($service->preferredImageUrl('cover'));
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
    }

    public function test_media_attaches_to_the_correct_collection(): void
    {
        Storage::fake('public');

        $service = Service::query()->create();

        $service->addMedia($this->pngUpload('cover.png'))->toMediaCollection('cover');
        $service->addMedia($this->pngUpload('og.png'))->toMediaCollection('og');

        $this->assertSame(1, $service->getMedia('cover')->count());
        $this->assertSame(1, $service->getMedia('og')->count());
        $this->assertSame('cover', $service->getFirstMedia('cover')->collection_name);
        $this->assertSame('og', $service->getFirstMedia('og')->collection_name);
    }

    public function test_force_deleting_a_model_deletes_attached_media(): void
    {
        Storage::fake('public');

        $service = Service::query()->create();
        $service->addMedia($this->pngUpload('cover.png'))->toMediaCollection('cover');

        $media = $service->getFirstMedia('cover');
        $path = $media->getPathRelativeToRoot();

        $service->delete();

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
        ]);
        Storage::disk('public')->assertExists($path);

        $service->forceDelete();

        $this->assertDatabaseMissing('media', [
            'id' => $media->id,
        ]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_media_collections_are_registered_as_expected(): void
    {
        $this->assertSame(['hero', 'content', 'og'], array_keys(Page::cmsMediaCollections()));
        $this->assertSame(['cover', 'content', 'og'], array_keys(Service::cmsMediaCollections()));
        $this->assertSame(['icon'], array_keys(Technology::cmsMediaCollections()));
        $this->assertSame(['cover'], array_keys(ProjectCategory::cmsMediaCollections()));
        $this->assertSame(['cover', 'gallery', 'og', 'documents'], array_keys(Project::cmsMediaCollections()));
        $this->assertSame(['cover', 'content', 'og'], array_keys(BlogPost::cmsMediaCollections()));
        $this->assertSame(['avatar'], array_keys(Testimonial::cmsMediaCollections()));

        $service = Service::query()->create();

        $this->assertTrue($service->getMediaCollection('cover')->singleFile);
        $this->assertTrue($service->getMediaCollection('og')->singleFile);
        $this->assertSame(config('media-library.disk_name'), $service->getMediaCollection('cover')->diskName);
    }

    public function test_non_admin_cannot_access_service_upload_flow(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get(ServiceResource::getUrl('create'))
            ->assertForbidden();
    }

    public function test_admin_can_upload_service_cover_media_through_filament_resource(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateService::class)
            ->fillForm([
                'sort_order' => 1,
                'status' => 'published',
                'is_featured' => false,
                'cover_upload' => $this->pngUpload('cover.png'),
                'translations' => [
                    'sk' => [
                        'slug' => 'servis',
                        'title' => 'Servis',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $service = Service::query()->firstOrFail();

        $this->assertTrue($service->hasMediaInCollection('cover'));
        $this->assertSame('cover', $service->getFirstMedia('cover')->collection_name);
    }

    protected function pngUpload(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
        );
    }
}
