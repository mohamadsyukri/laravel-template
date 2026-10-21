<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => seedAccess());

it('changes the password when the current one is right', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->put(route('user-password.update'), [
        'current_password' => 'password',
        'password' => 'kata-laluan-baharu-1',
        'password_confirmation' => 'kata-laluan-baharu-1',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('kata-laluan-baharu-1', $user->fresh()->password))->toBeTrue();
});

it('shows the error on the profile form when the current password is wrong', function (): void {
    $user = User::factory()->create();

    $form = [
        'current_password' => 'salah',
        'password' => 'kata-laluan-baharu-1',
        'password_confirmation' => 'kata-laluan-baharu-1',
    ];

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->put(route('user-password.update'), $form)
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrorsIn('updatePassword', 'current_password');

    // Follow the redirect in the same session: a second request would start a fresh one.
    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->followingRedirects()
        ->put(route('user-password.update'), $form)
        ->assertOk()
        ->assertSee('is-invalid', false)
        ->assertSee('Kata laluan yang diberi tidak sepadan dengan kata laluan semasa anda.');
});
