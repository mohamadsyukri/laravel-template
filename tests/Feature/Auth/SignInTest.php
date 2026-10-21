<?php

use App\Models\User;

beforeEach(fn () => seedAccess());

it('sends guests to the sign-in page', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('shows the sign-in page in Malay', function (): void {
    $this->get(route('login'))->assertOk()->assertSee('Log Masuk')->assertSee('Lupa kata laluan?');
});

it('signs in an active user and opens the dashboard', function (): void {
    $user = User::factory()->create(['email' => 'aminah@example.com']);

    $this->post(route('login'), ['email' => 'aminah@example.com', 'password' => 'password'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('rejects a wrong password with the generic message', function (): void {
    User::factory()->create(['email' => 'aminah@example.com']);

    $this->post(route('login'), ['email' => 'aminah@example.com', 'password' => 'wrong'])
        ->assertSessionHasErrors(['email' => 'Maklumat log masuk tidak sepadan dengan rekod kami.']);

    $this->assertGuest();
});

it('keeps an inactive user out, after checking the password', function (): void {
    User::factory()->inactive()->create(['email' => 'aminah@example.com']);

    $this->post(route('login'), ['email' => 'aminah@example.com', 'password' => 'password'])
        ->assertSessionHasErrors(['email' => 'Akaun anda tidak aktif. Sila hubungi pentadbir sistem.']);

    $this->assertGuest();
});

it('signs out', function (): void {
    $this->actingAs(User::factory()->create())->post(route('logout'))->assertRedirect('/');

    $this->assertGuest();
});

it('does not offer self-registration', function (): void {
    $this->get('/register')->assertNotFound();
});
