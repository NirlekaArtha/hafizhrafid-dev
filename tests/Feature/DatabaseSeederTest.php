<?php

declare(strict_types=1);

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\Category;
use App\Domain\Identity\Enums\UserRole;
use App\Domain\Localization\Enums\Locale;
use App\Domain\Portfolio\Models\Project;
use App\Domain\Portfolio\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\SampleContentSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder populates admin, categories, projects, articles, relations, and settings contract', function (): void {
    $this->seed(DatabaseSeeder::class);

    $admin = User::query()->where('email', 'admin@hafizhrafid.dev')->first();

    expect($admin)->not->toBeNull()
        ->and($admin?->role)->toBe(UserRole::Admin)
        ->and($admin?->isAdmin())->toBeTrue();

    expect(Category::query()->count())->toBe(4);

    $categories = Category::query()->ordered()->get();
    foreach ($categories as $category) {
        expect($category->getTranslation('name', 'id'))->not->toBeEmpty()
            ->and($category->getTranslation('name', 'en'))->not->toBeEmpty()
            ->and($category->getTranslation('slug', 'id'))->not->toBeEmpty()
            ->and($category->getTranslation('slug', 'en'))->not->toBeEmpty()
            ->and($category->getTranslation('description', 'id'))->not->toBeEmpty()
            ->and($category->getTranslation('description', 'en'))->not->toBeEmpty();
    }

    expect(Project::query()->count())->toBe(2)
        ->and(Project::query()->published()->count())->toBe(2)
        ->and(Project::query()->featured()->count())->toBe(2);

    $projects = Project::query()->with(['categories', 'articles'])->ordered()->get();
    foreach ($projects as $project) {
        expect($project->getTranslation('title', 'id'))->not->toBeEmpty()
            ->and($project->getTranslation('title', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('slug', 'id'))->not->toBeEmpty()
            ->and($project->getTranslation('slug', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('technical_decisions', 'id'))->not->toBeEmpty()
            ->and($project->getTranslation('technical_decisions', 'en'))->not->toBeEmpty()
            ->and($project->tech_stack)->toBeArray()->not->toBeEmpty()
            ->and($project->categories->count())->toBeGreaterThanOrEqual(1);
    }

    expect(
        $projects->sum(fn (Project $project): int => $project->articles->count())
    )->toBeGreaterThanOrEqual(2);

    expect(Article::query()->count())->toBe(3)
        ->and(Article::query()->published()->count())->toBe(3)
        ->and(Article::query()->forLocale(Locale::Id)->count())->toBe(2)
        ->and(Article::query()->forLocale(Locale::En)->count())->toBe(1);

    $articles = Article::query()->with(['categories', 'translations'])->get();
    foreach ($articles as $article) {
        expect($article->status)->toBe(ArticleStatus::Published)
            ->and($article->author_id)->toBe($admin?->id)
            ->and($article->view_count)->toBe(0)
            ->and($article->like_count)->toBe(0)
            ->and($article->reading_time_min)->toBeGreaterThan(0)
            ->and($article->body_html)->not->toContain('<h1')
            ->and($article->body_rendered)->not->toContain('<h1')
            ->and($article->toc)->toBeArray()->not->toBeEmpty()
            ->and($article->categories->count())->toBeGreaterThanOrEqual(1);
    }

    $translatedArticle = Article::query()->forLocale(Locale::En)->firstOrFail();
    expect($translatedArticle->is_machine_translated)->toBeTrue()
        ->and($translatedArticle->reviewed_at)->not->toBeNull()
        ->and($translatedArticle->translated_from_id)->not->toBeNull()
        ->and($translatedArticle->translations()->count())->toBe(2);

    $standaloneArticle = Article::query()
        ->forLocale(Locale::Id)
        ->where('translation_group_id', '!=', $translatedArticle->translation_group_id)
        ->first();

    expect($standaloneArticle)->not->toBeNull()
        ->and($standaloneArticle?->translations()->count())->toBe(1);

    $about = Setting::getValue('profile.about');
    $heroSubtitle = Setting::getValue('profile.hero_subtitle');
    $openToWork = Setting::getValue('profile.open_to_work');
    $skills = Setting::getValue('profile.skills');
    $timeline = Setting::getValue('profile.timeline');
    $socialLinks = Setting::getValue('social.links');
    $cvPaths = Setting::getValue('cv.paths');

    expect($about)->toBeArray()->toHaveKeys(['id', 'en'])
        ->and($heroSubtitle)->toBeArray()->toHaveKeys(['id', 'en'])
        ->and($openToWork)->toBeBool()
        ->and($skills)->toBeArray()->not->toBeEmpty()
        ->and($skills[0])->toHaveKeys(['group', 'items'])
        ->and($skills[0]['group'])->toHaveKeys(['id', 'en'])
        ->and($timeline)->toBeArray()->not->toBeEmpty()
        ->and($timeline[0])->toHaveKeys(['period', 'type', 'title', 'organization', 'description'])
        ->and($timeline[0]['title'])->toHaveKeys(['id', 'en'])
        ->and($timeline[0]['description'])->toHaveKeys(['id', 'en'])
        ->and($socialLinks)->toBeArray()->toHaveKeys(['github', 'linkedin', 'email'])
        ->and($socialLinks['email'])->toStartWith('mailto:')
        ->and($cvPaths)->toBeArray()->toHaveKeys(['id', 'en']);
});

test('database seeder is idempotent when executed multiple times', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->where('email', 'admin@hafizhrafid.dev')->count())->toBe(1)
        ->and(Category::query()->count())->toBe(4)
        ->and(Project::query()->count())->toBe(2)
        ->and(Article::query()->count())->toBe(3)
        ->and(Setting::query()->count())->toBe(7);
});

test('setting seeder preserves existing customized settings', function (): void {
    Setting::setValue('profile.open_to_work', false);
    Setting::setValue('profile.about', ['id' => 'Profil kustom', 'en' => 'Custom profile']);

    $this->seed(SettingSeeder::class);

    expect(Setting::query()->count())->toBe(7)
        ->and(Setting::getValue('profile.open_to_work'))->toBeFalse()
        ->and(Setting::getValue('profile.about'))->toBe(['id' => 'Profil kustom', 'en' => 'Custom profile']);
});

test('sample content seeder does not run in production', function (): void {
    $this->app->detectEnvironment(fn (): string => 'production');

    $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])
        ->assertSuccessful();
    $this->artisan('db:seed', ['--class' => SampleContentSeeder::class, '--force' => true])
        ->assertSuccessful();

    expect(Setting::query()->count())->toBe(7)
        ->and(User::query()->count())->toBe(0)
        ->and(Category::query()->count())->toBe(0)
        ->and(Project::query()->count())->toBe(0)
        ->and(Article::query()->count())->toBe(0);
});
