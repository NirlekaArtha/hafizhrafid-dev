<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Panel;
use Livewire\Livewire;

test('guests are redirected to the admin login page with the noindex header', function (): void {
    $this->get('/admin')
        ->assertRedirect('/admin/login')
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('admin login page is accessible and sends the noindex header', function (): void {
    $this->get('/admin/login')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('member users cannot access the admin panel and receive the noindex header', function (): void {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->get('/admin')
        ->assertForbidden()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('member users cannot sign in through the admin login page', function (): void {
    $member = User::factory()->create([
        'password' => 'secret-password',
    ]);

    Livewire::test(Login::class)
        ->set('data.email', $member->email)
        ->set('data.password', 'secret-password')
        ->call('authenticate')
        ->assertHasErrors(['data.email']);

    $this->assertGuest();
});

test('admin users can access the admin panel and receive the noindex header', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('canAccessPanel only permits admins on the admin panel', function (): void {
    $adminPanel = Panel::make()->id('admin');
    $otherPanel = Panel::make()->id('member');

    $admin = User::factory()->admin()->make();
    $member = User::factory()->make();

    expect($admin->canAccessPanel($adminPanel))->toBeTrue()
        ->and($admin->canAccessPanel($otherPanel))->toBeFalse()
        ->and($member->canAccessPanel($adminPanel))->toBeFalse();
});
