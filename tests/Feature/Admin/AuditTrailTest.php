<?php

use App\Models\Announcement;
use Spatie\Activitylog\Models\Activity;

beforeEach(function (): void {
    seedAccess();
    $this->admin = superAdmin();
});

it('lists recorded changes with who made them', function (): void {
    $this->actingAs($this->admin);
    Announcement::factory()->create();

    $this->getJson(route('admin.audit-trail.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonFragment(['causer_name' => $this->admin->name]);
});

it('shows the values before and after a change', function (): void {
    $this->actingAs($this->admin);
    $announcement = Announcement::factory()->create(['title' => 'Lama']);
    $announcement->update(['title' => 'Baharu']);

    $activity = Activity::query()->where('event', 'updated')->sole();

    $this->get(route('admin.audit-trail.show', $activity))
        ->assertOk()
        ->assertSeeInOrder(['title', 'Lama', 'Baharu']);
});

it('never records password hashes', function (): void {
    $this->actingAs($this->admin);
    $this->admin->update(['password' => 'kata-laluan-baharu']);

    expect(Activity::query()->where('subject_type', $this->admin->getMorphClass())->where('event', 'updated')->exists())->toBeFalse();
});
