<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

// Roles and permissions live in Spatie's pivot tables, which LogsActivity on a model never sees: before
// this, "who made X an administrator, and when?" had no answer in the audit trail.

beforeEach(function (): void {
    seedAccess();
    $this->admin = superAdmin();
    Role::findOrCreate('Clerk', 'web');
});

function accessChange(object $subject, string $event): Activity
{
    return Activity::query()
        ->where('subject_type', $subject->getMorphClass())
        ->where('subject_id', $subject->getKey())
        ->where('event', $event)
        ->whereNotNull('attribute_changes')
        ->latest('id')
        ->firstOrFail();
}

it("records who changed a user's roles, with the roles before and after", function (): void {
    $user = User::factory()->create();
    $user->assignRole(RoleSeeder::USER_ROLE);

    $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'roles' => [RoleSeeder::USER_ROLE, 'Clerk'],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    $activity = accessChange($user, 'updated');
    expect($activity->causer_id)->toBe($this->admin->id)
        ->and($activity->attribute_changes->get('old'))->toBe(['roles' => [RoleSeeder::USER_ROLE]])
        ->and($activity->attribute_changes->get('attributes'))->toBe(['roles' => ['Clerk', RoleSeeder::USER_ROLE]]);
});

it("records nothing about roles when a user's roles stay the same", function (): void {
    $user = User::factory()->create();
    $user->assignRole(RoleSeeder::USER_ROLE);

    $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'roles' => [RoleSeeder::USER_ROLE],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    // Creating the user is logged; an edit that changes nothing must add no "updated" entry.
    expect(Activity::query()
        ->where('subject_type', $user->getMorphClass())
        ->where('subject_id', $user->id)
        ->where('event', 'updated')
        ->exists())->toBeFalse();
});

it("records a role's permissions before and after a change", function (): void {
    $role = Role::findByName('Clerk', 'web');

    $this->actingAs($this->admin)->put(route('admin.roles.update', $role), [
        'name' => 'Clerk',
        'permissions' => ['admin.users.index'],
    ])->assertRedirect();

    $activity = accessChange($role, 'updated');
    expect($activity->attribute_changes->get('old'))->toBe(['permissions' => []])
        ->and($activity->attribute_changes->get('attributes'))->toBe(['permissions' => ['admin.users.index']]);
});

it('records a role being created and deleted, with its permissions', function (): void {
    $this->actingAs($this->admin)->post(route('admin.roles.store'), [
        'name' => 'Reviewer',
        'permissions' => ['admin.audit-trail.index'],
    ])->assertRedirect();
    $role = Role::findByName('Reviewer', 'web');

    expect(accessChange($role, 'created')->attribute_changes->get('attributes'))
        ->toBe(['name' => 'Reviewer', 'permissions' => ['admin.audit-trail.index']]);

    $this->actingAs($this->admin)->delete(route('admin.roles.destroy', $role))->assertRedirect();

    expect(accessChange($role, 'deleted')->attribute_changes->get('old'))
        ->toBe(['name' => 'Reviewer', 'permissions' => ['admin.audit-trail.index']]);
});

it('shows a role change on the audit trail screen', function (): void {
    $user = User::factory()->create();
    $user->assignRole(RoleSeeder::USER_ROLE);
    $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'roles' => ['Clerk'],
        'is_active' => '1',
    ]);

    $this->get(route('admin.audit-trail.show', accessChange($user, 'updated')))
        ->assertOk()
        ->assertSeeInOrder(['roles', RoleSeeder::USER_ROLE, 'Clerk']);
});
