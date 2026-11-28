<?php

declare(strict_types=1);

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\Category;
use App\Domain\Blog\Services\PhikiCodeHighlighter;
use App\Domain\Localization\Enums\Locale;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Models\User;
use App\Policies\ArticlePolicy;
use Filament\Actions\DeleteAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Livewire;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob;

function makeFakePngUpload(string $filename = 'cover.png'): UploadedFile
{
    // Minimal valid 1x1 transparent PNG so upload tests do not depend on ext-gd.
    $pngBinary = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        true,
    );

    return UploadedFile::fake()->createWithContent($filename, (string) $pngBinary);
}

test('guests are redirected to admin login when visiting articles resource', function (): void {
    $this->get(ArticleResource::getUrl('index'))
        ->assertRedirect('/admin/login')
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('member users cannot access article resource pages', function (): void {
    $member = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $member->id]);

    $this->actingAs($member)
        ->get(ArticleResource::getUrl('index'))
        ->assertForbidden()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($member)
        ->get(ArticleResource::getUrl('create'))
        ->assertForbidden()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($member)
        ->get(ArticleResource::getUrl('edit', ['record' => $article]))
        ->assertForbidden()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('article policy only permits admin users', function (): void {
    $admin = User::factory()->admin()->make();
    $member = User::factory()->make();
    $article = Article::factory()->make();
    $policy = new ArticlePolicy;

    expect(Gate::getPolicyFor(Article::class))->toBeInstanceOf(ArticlePolicy::class)
        ->and($policy->viewAny($admin))->toBeTrue()
        ->and($policy->view($admin, $article))->toBeTrue()
        ->and($policy->create($admin))->toBeTrue()
        ->and($policy->update($admin, $article))->toBeTrue()
        ->and($policy->delete($admin, $article))->toBeTrue()
        ->and($policy->deleteAny($admin))->toBeTrue()
        ->and($policy->viewAny($member))->toBeFalse()
        ->and($policy->view($member, $article))->toBeFalse()
        ->and($policy->create($member))->toBeFalse()
        ->and($policy->update($member, $article))->toBeFalse()
        ->and($policy->delete($member, $article))->toBeFalse()
        ->and($policy->deleteAny($member))->toBeFalse();
});

test('admin can render article resource pages with noindex header', function (): void {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->create(['author_id' => $admin->id]);

    $this->actingAs($admin)
        ->get(ArticleResource::getUrl('index'))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($admin)
        ->get(ArticleResource::getUrl('create'))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($admin)
        ->get(ArticleResource::getUrl('edit', ['record' => $article]))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('admin can list, search, and filter articles by locale, status, and category', function (): void {
    $admin = User::factory()->admin()->create();
    $webCategory = Category::factory()->create([
        'name' => ['id' => 'Web', 'en' => 'Web'],
        'slug' => ['id' => 'web', 'en' => 'web'],
    ]);
    $aiCategory = Category::factory()->create([
        'name' => ['id' => 'AI', 'en' => 'AI'],
        'slug' => ['id' => 'ai', 'en' => 'ai'],
    ]);

    $publishedId = Article::factory()->published()->forLocale(Locale::Id)->create([
        'title' => 'Arsitektur Monolith Modular',
        'slug' => 'arsitektur-monolith-modular',
        'author_id' => $admin->id,
    ]);
    $publishedId->categories()->attach($webCategory);

    $draftEn = Article::factory()->forLocale(Locale::En)->create([
        'title' => 'Evaluating LLM Pipelines',
        'slug' => 'evaluating-llm-pipelines',
        'status' => ArticleStatus::Draft,
        'author_id' => $admin->id,
    ]);
    $draftEn->categories()->attach($aiCategory);

    $this->actingAs($admin);

    Livewire::test(ListArticles::class)
        ->assertCanSeeTableRecords([$publishedId, $draftEn])
        ->searchTable('Monolith')
        ->assertCanSeeTableRecords([$publishedId])
        ->assertCanNotSeeTableRecords([$draftEn])
        ->searchTable('evaluating-llm-pipelines')
        ->assertCanSeeTableRecords([$draftEn])
        ->assertCanNotSeeTableRecords([$publishedId])
        ->searchTable('')
        ->filterTable('locale', Locale::Id->value)
        ->assertCanSeeTableRecords([$publishedId])
        ->assertCanNotSeeTableRecords([$draftEn])
        ->resetTableFilters()
        ->filterTable('status', ArticleStatus::Draft->value)
        ->assertCanSeeTableRecords([$draftEn])
        ->assertCanNotSeeTableRecords([$publishedId])
        ->resetTableFilters()
        ->filterTable('categories', [$aiCategory->getKey()])
        ->assertCanSeeTableRecords([$draftEn])
        ->assertCanNotSeeTableRecords([$publishedId]);
});

test('article form auto-generates slug only when slug is empty', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->set('data.title', 'Membangun Blog Dua Bahasa')
        ->assertFormSet([
            'slug' => 'membangun-blog-dua-bahasa',
        ])
        ->set('data.slug', 'slug-kustom-artikel')
        ->set('data.title', 'Judul Baru Tidak Menimpa Slug')
        ->assertFormSet([
            'slug' => 'slug-kustom-artikel',
        ]);
});

test('admin can create an article with categories and cover media', function (): void {
    Storage::fake('public');
    Queue::fake([PerformConversionsJob::class]);

    $admin = User::factory()->admin()->create();
    $categoryA = Category::factory()->create();
    $categoryB = Category::factory()->create();

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'locale' => Locale::Id->value,
            'title' => 'Panduan Laravel Modular',
            'slug' => 'panduan-laravel-modular',
            'excerpt' => 'Ringkasan singkat artikel.',
            'body_html' => '<p>Isi artikel percobaan.</p>',
            'status' => ArticleStatus::Published->value,
            'published_at' => null,
            'categories' => [$categoryA->getKey(), $categoryB->getKey()],
            'cover' => makeFakePngUpload('cover-1.png'),
            'meta_title' => 'SEO Laravel Modular',
            'meta_description' => 'Deskripsi meta artikel.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $article = Article::query()->first();

    expect($article)->not->toBeNull()
        ->and(Str::isUuid($article->translation_group_id))->toBeTrue()
        ->and($article->author_id)->toBe($admin->id)
        ->and($article->locale)->toBe(Locale::Id)
        ->and($article->slug)->toBe('panduan-laravel-modular')
        ->and($article->status)->toBe(ArticleStatus::Published)
        ->and($article->published_at)->not->toBeNull()
        ->and($article->categories()->pluck('categories.id')->sort()->values()->all())
        ->toBe(collect([$categoryA->getKey(), $categoryB->getKey()])->sort()->values()->all())
        ->and($article->getMedia('cover'))->toHaveCount(1)
        ->and($article->getFirstMedia('cover')?->disk)->toBe('public')
        ->and($article->meta_title)->toBe('SEO Laravel Modular')
        ->and($article->meta_description)->toBe('Deskripsi meta artikel.');

    Queue::assertPushed(PerformConversionsJob::class);
});

test('article model registers cover and content_images collections with webp and thumb conversions', function (): void {
    $article = new Article;
    $article->registerMediaCollections();
    $article->registerMediaConversions();

    $coverCollection = $article->getMediaCollection('cover');
    $contentCollection = $article->getMediaCollection('content_images');

    expect($coverCollection)->not->toBeNull()
        ->and($coverCollection?->singleFile)->toBeTrue()
        ->and($coverCollection?->diskName)->toBe('public')
        ->and($coverCollection?->acceptsMimeTypes)->toBe(['image/jpeg', 'image/png', 'image/webp'])
        ->and($contentCollection)->not->toBeNull()
        ->and($contentCollection?->singleFile)->toBeFalse()
        ->and($contentCollection?->diskName)->toBe('public')
        ->and(collect($article->mediaConversions)->map->getName()->all())->toBe(['webp', 'thumb']);
});

test('article form validates required fields, slug format, per-locale slug uniqueness, schedule rules, and cover mime', function (): void {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    Article::factory()->forLocale(Locale::Id)->create([
        'slug' => 'slug-sudah-ada',
        'author_id' => $admin->id,
    ]);

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'locale' => Locale::Id->value,
            'title' => '',
            'slug' => '',
            'status' => ArticleStatus::Scheduled->value,
            'published_at' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'title' => 'required',
            'slug' => 'required',
            'published_at' => 'required',
        ]);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'locale' => Locale::Id->value,
            'title' => 'Judul Tidak Valid',
            'slug' => 'Invalid Slug!',
            'status' => ArticleStatus::Scheduled->value,
            'published_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ])
        ->call('create')
        ->assertHasFormErrors([
            'slug' => 'regex',
            'published_at',
        ]);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'locale' => Locale::Id->value,
            'title' => 'Duplikat di Locale Sama',
            'slug' => 'slug-sudah-ada',
            'status' => ArticleStatus::Draft->value,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'slug',
        ]);

    // Same slug in a different locale is allowed.
    Livewire::test(CreateArticle::class)
        ->fillForm([
            'locale' => Locale::En->value,
            'title' => 'Same Slug Different Locale',
            'slug' => 'slug-sudah-ada',
            'status' => ArticleStatus::Draft->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    // Non-raster / SVG cover is rejected.
    Livewire::test(CreateArticle::class)
        ->fillForm([
            'locale' => Locale::En->value,
            'title' => 'SVG Cover Rejected',
            'slug' => 'svg-cover-rejected',
            'status' => ArticleStatus::Draft->value,
            'cover' => UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        ])
        ->call('create')
        ->assertHasFormErrors([
            'cover',
        ]);
});

test('admin can edit an article, replace single-file cover, enforce translation group locale uniqueness, and delete it', function (): void {
    Storage::fake('public');
    Queue::fake([PerformConversionsJob::class]);

    $admin = User::factory()->admin()->create();
    $groupId = (string) Str::uuid();

    $articleId = Article::factory()->forLocale(Locale::Id)->create([
        'translation_group_id' => $groupId,
        'title' => 'Artikel Asli',
        'slug' => 'artikel-asli',
        'status' => ArticleStatus::Draft,
        'author_id' => $admin->id,
    ]);
    $oldMedia = $articleId->addMedia(makeFakePngUpload('old-cover.png'))->toMediaCollection('cover', 'public');

    // Sibling translation in EN within the same translation_group_id.
    Article::factory()->forLocale(Locale::En)->create([
        'translation_group_id' => $groupId,
        'title' => 'Original Article',
        'slug' => 'original-article',
        'author_id' => $admin->id,
    ]);

    $this->actingAs($admin);

    // Changing locale to EN must fail because EN already exists in this translation group.
    Livewire::test(EditArticle::class, ['record' => $articleId->getRouteKey()])
        ->fillForm([
            'locale' => Locale::En->value,
        ])
        ->call('save')
        ->assertHasFormErrors(['locale']);

    $futureDate = now()->addDays(2)->startOfMinute();

    Livewire::test(EditArticle::class, ['record' => $articleId->getRouteKey()])
        ->fillForm([
            'locale' => Locale::Id->value,
            'title' => 'Artikel Diperbarui',
            'slug' => 'artikel-asli',
            'status' => ArticleStatus::Scheduled->value,
            'published_at' => $futureDate->format('Y-m-d H:i:s'),
            'cover' => makeFakePngUpload('new-cover.png'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $articleId->refresh();

    expect($articleId->title)->toBe('Artikel Diperbarui')
        ->and($articleId->status)->toBe(ArticleStatus::Scheduled)
        ->and($articleId->published_at?->format('Y-m-d H:i'))->toBe($futureDate->format('Y-m-d H:i'))
        ->and($articleId->getMedia('cover'))->toHaveCount(1)
        ->and($articleId->getFirstMedia('cover')?->getKey())->not->toBe($oldMedia->getKey())
        ->and($articleId->getFirstMedia('cover')?->name)->toBe('new-cover');

    Livewire::test(EditArticle::class, ['record' => $articleId->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($articleId);
});

test('article form configures CKEditor according to TDD 8.2 constraints', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $component = Livewire::test(CreateArticle::class);
    $field = $component->instance()->form->getFlatFields()['body_html'] ?? null;

    expect($field)->toBeInstanceOf(CKEditor::class);

    /** @var CKEditor $field */
    $options = $field->getEditorOptions();

    expect($options['licenseKey'] ?? null)->toBe('GPL')
        ->and($options['menuBar']['isVisible'] ?? null)->toBeFalse()
        ->and($field->getUploadUrl())->toBeNull()
        ->and($options)->not->toHaveKey('simpleUpload');

    // Headings must only allow paragraph and H2..H4 (H1 is reserved for the article title).
    $headingModels = array_column($options['heading']['options'] ?? [], 'model');
    $headingViews = array_values(array_filter(array_column($options['heading']['options'] ?? [], 'view')));

    expect($headingModels)->toBe(['paragraph', 'heading2', 'heading3', 'heading4'])
        ->and($headingViews)->toBe(['h2', 'h3', 'h4']);

    // Inline-style and raw-HTML plugins must stay disabled.
    $plugins = $options['plugins'] ?? [];
    $forbiddenPlugins = [
        'FontColor',
        'FontBackgroundColor',
        'FontSize',
        'FontFamily',
        'Highlight',
        'Style',
        'GeneralHtmlSupport',
        'SourceEditing',
        'HtmlEmbed',
        'Alignment',
        'ImageResize',
        'ListProperties',
        'TableProperties',
        'TableCellProperties',
    ];

    foreach ($forbiddenPlugins as $plugin) {
        expect($plugins)->not->toContain($plugin);
    }

    // Core writing plugins and image alt-text toolbar must be active.
    $toolbarItems = $options['toolbar']['items'] ?? [];
    $forbiddenToolbarItems = ['fontColor', 'fontBackgroundColor', 'fontSize', 'fontFamily', 'highlight', 'style', 'sourceEditing', 'alignment'];

    foreach ($forbiddenToolbarItems as $item) {
        expect($toolbarItems)->not->toContain($item);
    }

    expect($toolbarItems)->toContain('heading', 'bold', 'italic', 'code', 'link', 'insertTable', 'blockQuote', 'codeBlock', 'mediaEmbed', 'bulletedList', 'numberedList')
        ->and($options['image']['toolbar'] ?? [])->toContain('imageTextAlternative')
        ->and($options['image']['toolbar'] ?? [])->not->toContain('resizeImage')
        ->and($options['table']['contentToolbar'] ?? [])->toBe(['tableColumn', 'tableRow', 'mergeTableCells'])
        ->and($options['mediaEmbed']['previewsInData'] ?? null)->toBeFalse()
        ->and($options['mediaEmbed']['removeProviders'] ?? [])->toContain('facebook', 'instagram', 'twitter', 'spotify');

    // Every non-plaintext code block language must be supported by PhikiCodeHighlighter.
    $highlighter = new PhikiCodeHighlighter;
    $languages = array_column($options['codeBlock']['languages'] ?? [], 'language');

    expect($languages)->toContain('plaintext', 'php', 'js', 'ts', 'bash', 'python', 'sql', 'yaml', 'dockerfile');

    foreach ($languages as $language) {
        if ($language === 'plaintext') {
            continue;
        }

        $highlighted = $highlighter->highlight('x = 1', $language);
        expect($highlighted)->toContain('<span');
    }
});

test('public pages do not load CKEditor assets and temporary spike routes are removed', function (): void {
    $this->get('/id')
        ->assertOk()
        ->assertDontSee('filament-ckeditor-field', false);

    $this->get('/en')
        ->assertOk()
        ->assertDontSee('filament-ckeditor-field', false);

    expect(Route::has('ckeditor.upload'))->toBeFalse();
});
