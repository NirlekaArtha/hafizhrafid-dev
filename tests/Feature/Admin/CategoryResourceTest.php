<?php

declare(strict_types=1);

use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\Category;
use App\Domain\Portfolio\Models\Project;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\User;
use App\Policies\CategoryPolicy;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

test('guests are redirected to admin login when visiting categories resource', function (): void {
    $this->get(CategoryResource::getUrl('index'))
        ->assertRedirect('/admin/login')
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('member users cannot access category resource pages', function (): void {
    $member = User::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($member)
        ->get(CategoryResource::getUrl('index'))
        ->assertForbidden()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($member)
        ->get(CategoryResource::getUrl('create'))
        ->assertForbidden()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($member)
        ->get(CategoryResource::getUrl('edit', ['record' => $category]))
        ->assertForbidden()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('category policy only permits admin users', function (): void {
    $admin = User::factory()->admin()->make();
    $member = User::factory()->make();
    $category = Category::factory()->make();
    $policy = new CategoryPolicy;

    expect(Gate::getPolicyFor(Category::class))->toBeInstanceOf(CategoryPolicy::class)
        ->and($policy->viewAny($admin))->toBeTrue()
        ->and($policy->view($admin, $category))->toBeTrue()
        ->and($policy->create($admin))->toBeTrue()
        ->and($policy->update($admin, $category))->toBeTrue()
        ->and($policy->delete($admin, $category))->toBeTrue()
        ->and($policy->deleteAny($admin))->toBeTrue()
        ->and($policy->reorder($admin))->toBeTrue()
        ->and($policy->viewAny($member))->toBeFalse()
        ->and($policy->view($member, $category))->toBeFalse()
        ->and($policy->create($member))->toBeFalse()
        ->and($policy->update($member, $category))->toBeFalse()
        ->and($policy->delete($member, $category))->toBeFalse()
        ->and($policy->deleteAny($member))->toBeFalse()
        ->and($policy->reorder($member))->toBeFalse();
});

test('admin can render category resource pages with noindex header', function (): void {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin)
        ->get(CategoryResource::getUrl('index'))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($admin)
        ->get(CategoryResource::getUrl('create'))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->actingAs($admin)
        ->get(CategoryResource::getUrl('edit', ['record' => $category]))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('admin can list categories ordered by sort order with relation counts and search', function (): void {
    $admin = User::factory()->admin()->create();

    $second = Category::factory()->create([
        'name' => ['id' => 'Rekayasa Web', 'en' => 'Web Engineering'],
        'slug' => ['id' => 'rekayasa-web', 'en' => 'web-engineering'],
        'sort_order' => 2,
    ]);

    $first = Category::factory()->create([
        'name' => ['id' => 'Kecerdasan Buatan', 'en' => 'Artificial Intelligence'],
        'slug' => ['id' => 'kecerdasan-buatan', 'en' => 'artificial-intelligence'],
        'sort_order' => 1,
    ]);

    $article = Article::factory()->create(['author_id' => $admin->id]);
    $project = Project::factory()->create();
    $first->articles()->attach($article);
    $first->projects()->attach($project);

    $this->actingAs($admin);

    Livewire::test(ListCategories::class)
        ->assertCanSeeTableRecords([$first, $second], inOrder: true)
        ->assertTableColumnStateSet('articles_count', 1, $first)
        ->assertTableColumnStateSet('projects_count', 1, $first)
        ->searchTable('Kecerdasan')
        ->assertCanSeeTableRecords([$first])
        ->assertCanNotSeeTableRecords([$second])
        ->searchTable('Web Engineering')
        ->assertCanSeeTableRecords([$second])
        ->assertCanNotSeeTableRecords([$first])
        ->searchTable('kecerdasan-buatan')
        ->assertCanSeeTableRecords([$first])
        ->assertCanNotSeeTableRecords([$second])
        ->searchTable('web-engineering')
        ->assertCanSeeTableRecords([$second])
        ->assertCanNotSeeTableRecords([$first]);
});

test('admin can reorder categories in the table', function (): void {
    $admin = User::factory()->admin()->create();

    $first = Category::factory()->create(['sort_order' => 1]);
    $second = Category::factory()->create(['sort_order' => 2]);

    $this->actingAs($admin);

    Livewire::test(ListCategories::class)
        ->call('reorderTable', [(string) $second->getKey(), (string) $first->getKey()])
        ->assertCanSeeTableRecords([$second, $first], inOrder: true);

    expect($second->fresh()->sort_order)->toBeLessThan($first->fresh()->sort_order);
});

test('category form auto-generates slug only when slug is empty', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(CreateCategory::class)
        ->set('data.name.id', 'Arsitektur Perangkat Lunak')
        ->assertFormSet([
            'slug.id' => 'arsitektur-perangkat-lunak',
        ])
        ->set('data.slug.id', 'slug-kustom')
        ->set('data.name.id', 'Nama Baru Tidak Menimpa Slug')
        ->assertFormSet([
            'slug.id' => 'slug-kustom',
        ]);
});

test('admin can create a bilingual category', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => [
                'id' => 'Sistem Terdistribusi',
                'en' => 'Distributed Systems',
            ],
            'slug' => [
                'id' => 'sistem-terdistribusi',
                'en' => 'distributed-systems',
            ],
            'description' => [
                'id' => 'Catatan arsitektur sistem terdistribusi.',
                'en' => 'Notes on distributed systems architecture.',
            ],
            'sort_order' => 5,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $category = Category::query()->first();

    expect($category)->not->toBeNull()
        ->and($category->getTranslation('name', 'id'))->toBe('Sistem Terdistribusi')
        ->and($category->getTranslation('name', 'en'))->toBe('Distributed Systems')
        ->and($category->getTranslation('slug', 'id'))->toBe('sistem-terdistribusi')
        ->and($category->getTranslation('slug', 'en'))->toBe('distributed-systems')
        ->and($category->getTranslation('description', 'id'))->toBe('Catatan arsitektur sistem terdistribusi.')
        ->and($category->getTranslation('description', 'en'))->toBe('Notes on distributed systems architecture.')
        ->and($category->sort_order)->toBe(5);
});

test('category form validates required fields, slug format, and per-locale slug uniqueness', function (): void {
    $admin = User::factory()->admin()->create();

    Category::factory()->create([
        'name' => ['id' => 'DevOps', 'en' => 'DevOps'],
        'slug' => ['id' => 'devops-id', 'en' => 'devops-en'],
    ]);

    $this->actingAs($admin);

    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => ['id' => '', 'en' => ''],
            'slug' => ['id' => '', 'en' => ''],
            'sort_order' => -1,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name.id' => 'required',
            'name.en' => 'required',
            'slug.id' => 'required',
            'slug.en' => 'required',
            'sort_order' => 'min',
        ]);

    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => ['id' => 'Invalid Slug', 'en' => 'Invalid Slug'],
            'slug' => ['id' => 'Invalid Slug!', 'en' => 'UPPER_CASE'],
            'sort_order' => 0,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'slug.id' => 'regex',
            'slug.en' => 'regex',
        ]);

    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => ['id' => 'Duplikat', 'en' => 'Duplicate'],
            'slug' => ['id' => 'devops-id', 'en' => 'devops-en'],
            'sort_order' => 2147483648,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'slug.id',
            'slug.en',
            'sort_order' => 'max',
        ]);
});

test('admin can edit a category while keeping its own slug and delete it', function (): void {
    $admin = User::factory()->admin()->create();

    $category = Category::factory()->create([
        'name' => ['id' => 'Jaringan', 'en' => 'Networking'],
        'slug' => ['id' => 'jaringan', 'en' => 'networking'],
        'description' => ['id' => 'Deskripsi lama', 'en' => 'Old description'],
        'sort_order' => 3,
    ]);

    $this->actingAs($admin);

    Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
        ->assertFormSet([
            'name.id' => 'Jaringan',
            'name.en' => 'Networking',
            'slug.id' => 'jaringan',
            'slug.en' => 'networking',
            'sort_order' => 3,
        ])
        ->fillForm([
            'name' => ['id' => 'Jaringan & Keamanan', 'en' => 'Networking & Security'],
            'slug' => ['id' => 'jaringan', 'en' => 'networking'],
            'description' => ['id' => 'Deskripsi baru', 'en' => 'New description'],
            'sort_order' => 1,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $category->refresh();

    expect($category->getTranslation('name', 'id'))->toBe('Jaringan & Keamanan')
        ->and($category->getTranslation('name', 'en'))->toBe('Networking & Security')
        ->and($category->getTranslation('description', 'en'))->toBe('New description')
        ->and($category->sort_order)->toBe(1);

    Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($category);
});
