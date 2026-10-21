<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    seedAccess();
    $this->admin = superAdmin();
});

it('lists users with their roles', function (): void {
    User::factory()->create(['name' => 'Siti Aminah'])->assignRole('Pengguna');

    $this->actingAs($this->admin)
        ->getJson(route('admin.users.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Siti Aminah', 'roles' => 'Pengguna']);
});

it('creates a user with roles', function (): void {
    $this->actingAs($this->admin)->post(route('admin.users.store'), [
        'name' => 'Siti Aminah',
        'email' => 'Siti@Example.com',
        'password' => 'rahsia-baharu-123',
        'password_confirmation' => 'rahsia-baharu-123',
        'roles' => ['Pengguna'],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'siti@example.com')->sole();
    expect($user->hasRole('Pengguna'))->toBeTrue()
        ->and($user->is_active)->toBeTrue()
        ->and(Hash::check('rahsia-baharu-123', $user->password))->toBeTrue();
});

it('rejects a duplicate email and a missing role', function (): void {
    User::factory()->create(['email' => 'siti@example.com']);

    $this->actingAs($this->admin)->post(route('admin.users.store'), [
        'name' => 'Siti',
        'email' => 'siti@example.com',
        'password' => 'rahsia-baharu-123',
        'password_confirmation' => 'rahsia-baharu-123',
        'roles' => [],
    ])->assertSessionHasErrors(['email', 'roles']);
});

it('keeps the current password when the field is left blank', function (): void {
    $user = User::factory()->create();
    $oldHash = $user->password;

    $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
        'name' => 'Nama Baharu',
        'email' => $user->email,
        'password' => '',
        'password_confirmation' => '',
        'roles' => ['Pengguna'],
        'is_active' => '0',
    ])->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->name)->toBe('Nama Baharu')
        ->and($user->password)->toBe($oldHash)
        ->and($user->is_active)->toBeFalse();
});

it('stops an administrator from locking themselves out', function (): void {
    $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
        'name' => $this->admin->name,
        'email' => $this->admin->email,
        'roles' => ['Pengguna'],
        'is_active' => '0',
    ])->assertSessionHasErrors(['is_active', 'roles']);

    expect($this->admin->fresh()->hasRole(config('access.super_admin_role')))->toBeTrue();
});
