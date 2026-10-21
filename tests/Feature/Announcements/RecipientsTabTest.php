<?php

use App\Livewire\Announcements\RecipientsTab;
use App\Models\Announcement;
use Livewire\Livewire;

beforeEach(function (): void {
    seedAccess();
    $this->editor = userWithPermissions('announcements.edit');
    $this->announcement = Announcement::factory()->create();
});

// 6. Child-record sync
it('starts with one empty row when there are no recipients', function (): void {
    Livewire::actingAs($this->editor)
        ->test(RecipientsTab::class, ['announcement' => $this->announcement])
        ->assertSet('rows', [['name' => '', 'email' => '']]);
});

it('replaces the recipient list on save', function (): void {
    $this->announcement->recipients()->create(['name' => 'Lama', 'email' => 'lama@example.com']);

    Livewire::actingAs($this->editor)
        ->test(RecipientsTab::class, ['announcement' => $this->announcement])
        ->assertSet('rows.0.email', 'lama@example.com')
        ->call('removeRow', 0)
        ->call('addRow')
        ->set('rows.0', ['name' => 'Siti', 'email' => 'siti@example.com'])
        ->call('addRow')
        ->set('rows.1', ['name' => 'Ali', 'email' => 'ali@example.com'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    expect($this->announcement->recipients()->pluck('email')->all())->toBe(['siti@example.com', 'ali@example.com']);
});

it('rejects a bad or repeated email and keeps the old list', function (): void {
    $this->announcement->recipients()->create(['name' => 'Lama', 'email' => 'lama@example.com']);

    Livewire::actingAs($this->editor)
        ->test(RecipientsTab::class, ['announcement' => $this->announcement])
        ->set('rows', [
            ['name' => 'Siti', 'email' => 'bukan-emel'],
            ['name' => 'Ali', 'email' => 'ali@example.com'],
            ['name' => 'Ali lagi', 'email' => 'ali@example.com'],
        ])
        ->call('save')
        ->assertHasErrors(['rows.0.email', 'rows.1.email', 'rows.2.email']);

    expect($this->announcement->recipients()->pluck('email')->all())->toBe(['lama@example.com']);
});

it('refuses to save for a user without the edit permission', function (): void {
    Livewire::actingAs(userWithPermissions('announcements.show'))
        ->test(RecipientsTab::class, ['announcement' => $this->announcement])
        ->call('save')
        ->assertForbidden();
});

it('refuses to save once the announcement is published', function (): void {
    $published = Announcement::factory()->published()->create();

    Livewire::actingAs($this->editor)
        ->test(RecipientsTab::class, ['announcement' => $published])
        ->set('rows.0', ['name' => 'Siti', 'email' => 'siti@example.com'])
        ->call('save')
        ->assertForbidden();

    expect($published->recipients()->count())->toBe(0);
});
