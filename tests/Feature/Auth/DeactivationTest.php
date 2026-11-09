<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RoleSeeder;

// Before this, is_active was checked only on the sign-in form: a user already signed in, or holding a
// "Remember me" cookie (400 days by default), kept working after an administrator switched them off.

beforeEach(function (): void {
    seedAccess();
});

it('signs a user out on their next request once they are deactivated', function (): void {
    $user = User::factory()->create();

    $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])->assertRedirect();
    $this->get(route('profile.edit'))->assertOk();

    // An administrator switches the account off elsewhere; the next request reads the user from the
    // session again, as a real request would.
    User::query()->whereKey($user->id)->update(['is_active' => false]);
    auth()->forgetGuards();

    $this->get(route('profile.edit'))->assertRedirect(route('login'));
    $this->assertGuest();
});

it('ends every "Remember me" sign-in when an administrator deactivates the account', function (): void {
    $user = User::factory()->create();
    $user->assignRole(RoleSeeder::USER_ROLE);
    $user->setRememberToken('remembered-token');
    $user->save();

    $this->actingAs(superAdmin())->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'roles' => [RoleSeeder::USER_ROLE],
        'is_active' => '0',
    ])->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->getRememberToken())->not->toBe('remembered-token');
});

it('keeps the "Remember me" sign-in on an ordinary edit', function (): void {
    $user = User::factory()->create();
    $user->assignRole(RoleSeeder::USER_ROLE);
    $user->setRememberToken('remembered-token');
    $user->save();

    $this->actingAs(superAdmin())->put(route('admin.users.update', $user), [
        'name' => 'Renamed User',
        'email' => $user->email,
        'roles' => [RoleSeeder::USER_ROLE],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->getRememberToken())->toBe('remembered-token');
});
