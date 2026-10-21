<?php

use App\Enums\AnnouncementStatus;
use App\Exports\AnnouncementsExport;
use App\Models\Announcement;
use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

beforeEach(function (): void {
    seedAccess();
    $this->editor = userWithPermissions('announcements.index', 'announcements.show', 'announcements.create', 'announcements.edit', 'announcements.destroy');
});

/**
 * @return array<string, string>
 */
function announcementForm(array $overrides = []): array
{
    return [
        'title' => 'Cuti Umum Deepavali',
        'body' => 'Pejabat ditutup pada hari Deepavali.',
        'starts_on' => '2026-11-01',
        'ends_on' => '2026-11-08',
        'form_action' => 'save',
        ...$overrides,
    ];
}

// 1. Access gate
it('refuses the form to a user who may only read', function (): void {
    $reader = userWithPermissions('announcements.index');

    $this->actingAs($reader)->get(route('announcements.create'))->assertForbidden();
    $this->actingAs($reader)->post(route('announcements.store'), announcementForm())->assertForbidden();
    expect(Announcement::query()->count())->toBe(0);
});

// 2. Missing record
it('answers 404 for an announcement that does not exist', function (): void {
    $this->actingAs($this->editor)->get(route('announcements.show', 999))->assertNotFound();
});

// 3. Happy save
it('saves a draft, records the author and returns to the form', function (): void {
    $response = $this->actingAs($this->editor)->post(route('announcements.store'), announcementForm());

    $announcement = Announcement::query()->sole();
    $response->assertRedirect(route('announcements.edit', $announcement))->assertSessionHas('success', 'Pengumuman telah disimpan.');

    expect($announcement->status)->toBe(AnnouncementStatus::Draft)
        ->and($announcement->starts_on->toDateString())->toBe('2026-11-01')
        ->and($announcement->published_at)->toBeNull()
        ->and($announcement->created_by)->toBe($this->editor->id);
});

// 4. Validation rejections
it('rejects incomplete or inconsistent input', function (array $input, string $field): void {
    $this->actingAs($this->editor)
        ->post(route('announcements.store'), announcementForm($input))
        ->assertSessionHasErrors($field);

    expect(Announcement::query()->count())->toBe(0);
})->with([
    'no title' => [['title' => ''], 'title'],
    'title too long' => [['title' => str_repeat('a', 201)], 'title'],
    'no content' => [['body' => ''], 'body'],
    'bad start date' => [['starts_on' => '01/11/2026'], 'starts_on'],
    'ends before it starts' => [['ends_on' => '2026-10-01'], 'ends_on'],
    'unknown button' => [['form_action' => 'approve'], 'form_action'],
]);

// 5. State transition
it('publishes from the form and then shows it read-only', function (): void {
    $draft = Announcement::factory()->for($this->editor, 'author')->create();

    $this->actingAs($this->editor)
        ->put(route('announcements.update', $draft), announcementForm(['form_action' => 'publish']))
        ->assertRedirect(route('announcements.show', $draft));

    $draft->refresh();
    expect($draft->status)->toBe(AnnouncementStatus::Published)
        ->and($draft->published_at)->not->toBeNull();
});

it('freezes a published announcement for everyone', function (): void {
    $published = Announcement::factory()->published()->create();

    $this->actingAs($this->editor)->get(route('announcements.edit', $published))->assertForbidden();
    $this->actingAs($this->editor)->put(route('announcements.update', $published), announcementForm())->assertForbidden();
    $this->actingAs(superAdmin())->delete(route('announcements.destroy', $published))->assertForbidden();

    expect($published->fresh()->title)->toBe($published->title);
});

// 7. Delete rules
it('deletes a draft', function (): void {
    $draft = Announcement::factory()->create();

    $this->actingAs($this->editor)
        ->delete(route('announcements.destroy', $draft))
        ->assertRedirect(route('announcements.index'));

    expect(Announcement::query()->count())->toBe(0);
});

// 8. Links and listing
it('shows the Add button only to users who may create', function (): void {
    $this->actingAs($this->editor)->get(route('announcements.index'))->assertSee(route('announcements.create'));
    $this->actingAs(userWithPermissions('announcements.index'))->get(route('announcements.index'))->assertDontSee(route('announcements.create'));
});

it('feeds the table with the filtered rows', function (): void {
    Announcement::factory()->create(['title' => 'Draf A']);
    Announcement::factory()->published()->create(['title' => 'Terbit B']);

    $this->actingAs($this->editor)
        ->getJson(route('announcements.datatable', ['draw' => 1, 'filter_status' => 'published']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Terbit B');
});

it('exports the filtered rows to Excel', function (): void {
    Excel::fake();
    Announcement::factory()->create();
    Announcement::factory()->published()->create();

    $this->actingAs($this->editor)->get(route('announcements.export', ['filter_status' => 'draft']))->assertOk();

    Excel::assertDownloaded('pengumuman-'.now()->format('Ymd').'.xlsx', fn (AnnouncementsExport $export): bool => $export->query()->count() === 1);
});

it('prints the filtered rows to PDF', function (): void {
    SnappyPdf::fake();
    Announcement::factory()->create(['title' => 'Draf A']);
    Announcement::factory()->published()->create(['title' => 'Terbit B']);

    $this->actingAs($this->editor)->get(route('announcements.pdf', ['filter_status' => 'published']))->assertOk();

    SnappyPdf::assertViewIs('modules.announcements.pdf');
    SnappyPdf::assertSee('Terbit B');
    SnappyPdf::assertDontSee('Draf A');
});

it('records each change in the audit trail with the old and new values', function (): void {
    $draft = Announcement::factory()->create(['title' => 'Lama']);

    $this->actingAs($this->editor)->put(route('announcements.update', $draft), announcementForm(['title' => 'Baharu']));

    $activity = Activity::query()->where('subject_type', $draft->getMorphClass())->where('event', 'updated')->sole();
    expect($activity->causer_id)->toBe($this->editor->id)
        ->and($activity->attribute_changes['old']['title'])->toBe('Lama')
        ->and($activity->attribute_changes['attributes']['title'])->toBe('Baharu');
});

it('lists only published announcements on the dashboard', function (): void {
    Announcement::factory()->create(['title' => 'Masih draf']);
    Announcement::factory()->published()->create(['title' => 'Sudah terbit']);

    $this->actingAs(User::factory()->create()->assignRole('Pengguna'))
        ->get(route('dashboard'))
        ->assertSee('Sudah terbit')
        ->assertDontSee('Masih draf');
});
