<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Tests\TestCase;

class CmsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_foundation_tables_exist_after_migrations_run(): void
    {
        $tables = [
            'pages',
            'page_translations',
            'services',
            'service_translations',
            'technologies',
            'technology_translations',
            'project_categories',
            'project_category_translations',
            'projects',
            'project_translations',
            'project_service',
            'project_technology',
            'blog_categories',
            'blog_category_translations',
            'blog_posts',
            'blog_post_translations',
            'testimonials',
            'testimonial_translations',
            'faqs',
            'faq_translations',
            'tags',
            'tag_translations',
            'taggables',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected table [{$table}] to exist.");
        }
    }

    public function test_translation_uniqueness_constraints_are_enforced(): void
    {
        $page = Page::query()->create();

        PageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'sk',
            'slug' => 'home',
            'title' => 'Home',
        ]);

        $this->expectException(QueryException::class);

        PageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'sk',
            'slug' => 'homepage',
            'title' => 'Homepage',
        ]);
    }

    public function test_locale_slug_uniqueness_constraints_are_enforced(): void
    {
        PageTranslation::query()->create([
            'page_id' => Page::query()->create()->id,
            'locale' => 'en',
            'slug' => 'about',
            'title' => 'About',
        ]);

        $this->expectException(QueryException::class);

        PageTranslation::query()->create([
            'page_id' => Page::query()->create()->id,
            'locale' => 'en',
            'slug' => 'about',
            'title' => 'About duplicate',
        ]);
    }

    public function test_project_can_attach_services_and_technologies(): void
    {
        $project = Project::query()->create();
        $service = Service::query()->create();
        $technology = Technology::query()->create(['key' => 'laravel']);

        $project->services()->attach($service);
        $project->technologies()->attach($technology);

        $this->assertTrue($project->services()->whereKey($service)->exists());
        $this->assertTrue($project->technologies()->whereKey($technology)->exists());
        $this->assertTrue($service->projects()->whereKey($project)->exists());
        $this->assertTrue($technology->projects()->whereKey($project)->exists());
    }

    public function test_blog_post_belongs_to_category_and_author(): void
    {
        $category = BlogCategory::query()->create();
        $author = User::factory()->create();

        $post = BlogPost::query()->create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        $this->assertTrue($post->category->is($category));
        $this->assertTrue($post->author->is($author));
        $this->assertTrue($category->posts()->whereKey($post)->exists());
        $this->assertTrue($author->authoredBlogPosts()->whereKey($post)->exists());
    }

    public function test_tags_work_polymorphically_for_project_and_blog_post(): void
    {
        $tag = Tag::query()->create(['key' => 'featured']);
        $project = Project::query()->create();
        $post = BlogPost::query()->create();

        $project->tags()->attach($tag);
        $post->tags()->attach($tag);

        $this->assertTrue($project->tags()->whereKey($tag)->exists());
        $this->assertTrue($post->tags()->whereKey($tag)->exists());
        $this->assertTrue($tag->projects()->whereKey($project)->exists());
        $this->assertTrue($tag->blogPosts()->whereKey($post)->exists());
    }

    public function test_tags_work_polymorphically_for_services(): void
    {
        $tag = Tag::query()->create(['key' => 'consulting']);
        $service = Service::query()->create();

        $service->tags()->attach($tag);

        $this->assertTrue($service->tags()->whereKey($tag)->exists());
        $this->assertTrue($tag->services()->whereKey($service)->exists());
    }

    public function test_nullable_relations_are_cleared_when_related_models_are_deleted(): void
    {
        $creator = User::factory()->create();
        $updater = User::factory()->create();
        $page = Page::query()->create([
            'created_by' => $creator->id,
            'updated_by' => $updater->id,
        ]);

        $creator->delete();
        $updater->delete();

        $page->refresh();
        $this->assertNull($page->created_by);
        $this->assertNull($page->updated_by);

        $category = BlogCategory::query()->create();
        $author = User::factory()->create();
        $post = BlogPost::query()->create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        $category->forceDelete();
        $author->delete();

        $post->refresh();
        $this->assertNull($post->blog_category_id);
        $this->assertNull($post->author_id);

        $projectCategory = ProjectCategory::query()->create();
        $project = Project::query()->create([
            'project_category_id' => $projectCategory->id,
        ]);
        $testimonial = Testimonial::query()->create([
            'client_name' => 'Client',
            'project_id' => $project->id,
        ]);

        $projectCategory->forceDelete();

        $project->refresh();
        $this->assertNull($project->project_category_id);

        $project->forceDelete();

        $testimonial->refresh();
        $this->assertNull($testimonial->project_id);

        $service = Service::query()->create();
        $faq = Faq::query()->create([
            'service_id' => $service->id,
        ]);

        $service->forceDelete();

        $faq->refresh();
        $this->assertNull($faq->service_id);
    }

    public function test_testimonial_rating_must_be_between_one_and_five(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Testimonial::query()->create([
            'client_name' => 'Client',
            'rating' => 6,
        ]);
    }

    public function test_soft_deletes_are_enabled_on_major_content_models(): void
    {
        $models = [
            Page::query()->create(),
            Service::query()->create(),
            Technology::query()->create(['key' => 'vue']),
            ProjectCategory::query()->create(),
            Project::query()->create(),
            BlogCategory::query()->create(),
            BlogPost::query()->create(),
            Testimonial::query()->create(['client_name' => 'Client']),
            Faq::query()->create(),
            Tag::query()->create(['key' => 'case-study']),
        ];

        foreach ($models as $model) {
            $model->delete();

            $this->assertSoftDeleted($model);
        }
    }
}
