<?php

declare(strict_types=1);

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\ArticleRevision;
use App\Domain\Blog\Models\Category;
use App\Domain\Contact\Models\ContactMessage;
use App\Domain\Identity\Enums\UserRole;
use App\Domain\Localization\Enums\Locale;
use App\Domain\Portfolio\Models\Project;
use App\Domain\Portfolio\Models\Setting;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('phase one migrations create all expected tables and key columns', function (): void {
    expect(Schema::hasColumns('users', ['id', 'name', 'email', 'avatar_url', 'role']))->toBeTrue()
        ->and(Schema::hasColumns('categories', ['id', 'name', 'slug', 'description', 'sort_order']))->toBeTrue()
        ->and(Schema::hasColumns('articles', [
            'id', 'translation_group_id', 'locale', 'slug', 'title', 'excerpt',
            'body_html', 'body_rendered', 'toc', 'status', 'published_at',
            'reading_time_min', 'meta_title', 'meta_description',
            'is_machine_translated', 'reviewed_at', 'translated_from_id',
            'view_count', 'like_count', 'author_id',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('article_category', ['article_id', 'category_id']))->toBeTrue()
        ->and(Schema::hasColumns('article_revisions', ['id', 'article_id', 'title', 'body_html', 'created_at']))->toBeTrue()
        ->and(Schema::hasColumns('projects', [
            'id', 'title', 'slug', 'summary', 'problem', 'solution', 'role',
            'technical_decisions', 'result', 'tech_stack', 'demo_url', 'repo_url',
            'is_featured', 'sort_order', 'published_at',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('category_project', ['category_id', 'project_id']))->toBeTrue()
        ->and(Schema::hasColumns('article_project', ['article_id', 'project_id']))->toBeTrue()
        ->and(Schema::hasColumns('contact_messages', ['id', 'name', 'email', 'message', 'ip_hash', 'read_at']))->toBeTrue()
        ->and(Schema::hasColumns('settings', ['key', 'value']))->toBeTrue();
});

test('user defaults to member role, protects role from mass assignment, and relates to articles', function (): void {
    $member = User::factory()->create();
    $admin = User::factory()->admin()->create();

    expect($member->role)->toBe(UserRole::Member)
        ->and($member->isAdmin())->toBeFalse()
        ->and($admin->role)->toBe(UserRole::Admin)
        ->and($admin->isAdmin())->toBeTrue();

    $member->fill(['role' => UserRole::Admin->value, 'avatar_url' => 'https://example.com/avatar.webp']);
    expect($member->role)->toBe(UserRole::Member)
        ->and($member->avatar_url)->toBe('https://example.com/avatar.webp');

    $article = Article::factory()->for($admin, 'author')->create();
    expect($admin->articles->pluck('id')->all())->toBe([$article->id]);
});

test('article defaults, counter protection, relations, and scopes work as specified', function (): void {
    $author = User::factory()->admin()->create();
    $groupId = (string) Str::uuid();

    $draft = Article::factory()->for($author, 'author')->create([
        'translation_group_id' => $groupId,
        'locale' => Locale::Id->value,
        'slug' => 'arsitektur-blog',
    ]);

    expect($draft->status)->toBe(ArticleStatus::Draft)
        ->and($draft->locale)->toBe(Locale::Id)
        ->and($draft->view_count)->toBe(0)
        ->and($draft->like_count)->toBe(0)
        ->and($draft->is_machine_translated)->toBeFalse();

    $draft->fill(['view_count' => 999, 'like_count' => 888]);
    expect($draft->view_count)->toBe(0)
        ->and($draft->like_count)->toBe(0);

    $publishedEn = Article::factory()->published()->for($author, 'author')->create([
        'translation_group_id' => $groupId,
        'locale' => Locale::En->value,
        'slug' => 'arsitektur-blog',
        'is_machine_translated' => true,
        'reviewed_at' => now(),
        'translated_from_id' => $draft->id,
    ]);

    $futurePublished = Article::factory()->for($author, 'author')->create([
        'locale' => Locale::En->value,
        'status' => ArticleStatus::Published->value,
        'published_at' => now()->addHour(),
    ]);

    $scheduled = Article::factory()->scheduled()->for($author, 'author')->create([
        'locale' => Locale::En->value,
    ]);

    expect($publishedEn->translatedFrom?->is($draft))->toBeTrue()
        ->and($publishedEn->translations()->pluck('id')->sort()->values()->all())
        ->toBe([$draft->id, $publishedEn->id])
        ->and(Article::published()->pluck('id')->all())->toBe([$publishedEn->id])
        ->and(Article::forLocale(Locale::Id)->pluck('id')->all())->toBe([$draft->id])
        ->and(Article::forLocale('en')->pluck('id')->sort()->values()->all())
        ->toBe([$publishedEn->id, $futurePublished->id, $scheduled->id]);
});

test('article enforces unique locale+slug and unique translation_group_id+locale constraints', function (): void {
    $author = User::factory()->admin()->create();
    $groupId = (string) Str::uuid();

    Article::factory()->for($author, 'author')->create([
        'translation_group_id' => $groupId,
        'locale' => Locale::Id->value,
        'slug' => 'catatan-laravel',
    ]);

    expect(fn () => Article::factory()->for($author, 'author')->create([
        'locale' => Locale::Id->value,
        'slug' => 'catatan-laravel',
    ]))->toThrow(QueryException::class);

    expect(fn () => Article::factory()->for($author, 'author')->create([
        'translation_group_id' => $groupId,
        'locale' => Locale::Id->value,
        'slug' => 'slug-berbeda',
    ]))->toThrow(QueryException::class);
});

test('article revisions omit updated_at and cascade delete with their article', function (): void {
    $article = Article::factory()->create();
    $revision = ArticleRevision::factory()->for($article)->create([
        'title' => 'First Draft',
        'body_html' => '<p>Initial content</p>',
    ]);

    expect(ArticleRevision::UPDATED_AT)->toBeNull()
        ->and($revision->article->is($article))->toBeTrue()
        ->and($article->revisions)->toHaveCount(1);

    $article->delete();

    expect(ArticleRevision::query()->whereKey($revision->id)->exists())->toBeFalse();
});

test('categories and projects support translations, pivots, and scopes', function (): void {
    $backend = Category::factory()->create([
        'name' => ['id' => 'Backend', 'en' => 'Backend'],
        'slug' => ['id' => 'backend', 'en' => 'backend'],
        'sort_order' => 2,
    ]);
    $devops = Category::factory()->create([
        'name' => ['id' => 'Infrastruktur', 'en' => 'Infrastructure'],
        'slug' => ['id' => 'infrastruktur', 'en' => 'infrastructure'],
        'sort_order' => 1,
    ]);

    expect($devops->getTranslation('name', 'id'))->toBe('Infrastruktur')
        ->and($devops->getTranslation('name', 'en'))->toBe('Infrastructure')
        ->and(Category::ordered()->pluck('id')->all())->toBe([$devops->id, $backend->id]);

    $featuredProject = Project::factory()->published()->featured()->create([
        'title' => ['id' => 'Web Pribadi', 'en' => 'Personal Website'],
        'slug' => ['id' => 'web-pribadi', 'en' => 'personal-website'],
        'technical_decisions' => [
            'id' => 'Memilih modular monolith.',
            'en' => 'Chose a modular monolith.',
        ],
        'tech_stack' => ['Laravel', 'Livewire', 'PostgreSQL'],
        'sort_order' => 1,
    ]);
    $draftProject = Project::factory()->create(['sort_order' => 2]);

    $article = Article::factory()->published()->create();

    $featuredProject->categories()->attach([$devops->id, $backend->id]);
    $featuredProject->articles()->attach($article->id);
    $article->categories()->attach($backend->id);

    expect($featuredProject->getTranslation('technical_decisions', 'id'))->toBe('Memilih modular monolith.')
        ->and($featuredProject->getTranslation('technical_decisions', 'en'))->toBe('Chose a modular monolith.')
        ->and($featuredProject->tech_stack)->toBe(['Laravel', 'Livewire', 'PostgreSQL'])
        ->and($featuredProject->categories)->toHaveCount(2)
        ->and($featuredProject->articles->pluck('id')->all())->toBe([$article->id])
        ->and($article->projects->pluck('id')->all())->toBe([$featuredProject->id])
        ->and($backend->articles->pluck('id')->all())->toBe([$article->id])
        ->and($backend->projects->pluck('id')->all())->toBe([$featuredProject->id])
        ->and(Project::published()->featured()->ordered()->pluck('id')->all())->toBe([$featuredProject->id])
        ->and(Project::ordered()->pluck('id')->all())->toBe([$featuredProject->id, $draftProject->id]);
});

test('contact messages store hashed ip, protect read_at from mass assignment, and filter unread rows', function (): void {
    $unread = ContactMessage::factory()->create();
    $read = ContactMessage::factory()->read()->create();

    $unread->fill(['read_at' => now()]);

    expect($unread->ip_hash)->toHaveLength(64)
        ->and($unread->read_at)->toBeNull()
        ->and($read->read_at)->not->toBeNull()
        ->and(ContactMessage::unread()->pluck('id')->all())->toBe([$unread->id]);
});

test('settings use string key primary key and persist boolean, object, and list json values', function (): void {
    Setting::setValue('profile.open_to_work', true);
    Setting::setValue('profile.about', ['id' => 'Halo', 'en' => 'Hello']);
    Setting::setValue('profile.skills', [
        ['group' => ['id' => 'Backend', 'en' => 'Backend'], 'items' => ['Laravel', 'PostgreSQL']],
    ]);

    expect(Setting::getValue('profile.open_to_work'))->toBeTrue()
        ->and(Setting::getValue('profile.about'))->toBe(['id' => 'Halo', 'en' => 'Hello'])
        ->and(Setting::getValue('profile.skills'))->toBe([
            ['group' => ['id' => 'Backend', 'en' => 'Backend'], 'items' => ['Laravel', 'PostgreSQL']],
        ])
        ->and(Setting::getValue('profile.missing', 'fallback'))->toBe('fallback');

    Setting::setValue('profile.open_to_work', false);
    expect(Setting::getValue('profile.open_to_work'))->toBeFalse()
        ->and(Setting::query()->count())->toBe(3);
});
