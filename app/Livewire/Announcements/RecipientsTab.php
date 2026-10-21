<?php

declare(strict_types=1);

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use App\Services\Announcements\AnnouncementService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The one Livewire piece of the module: a list of rows the user adds and removes before saving.
 * That per-row state is what Livewire is for; the rest of the module is plain Blade.
 */
final class RecipientsTab extends Component
{
    #[Locked]
    public int $announcementId;

    /** @var list<array{name: string, email: string}> */
    public array $rows = [];

    public function mount(Announcement $announcement): void
    {
        $this->announcementId = $announcement->id;
        $this->rows = $announcement->recipients()->get(['name', 'email'])->map(
            fn ($recipient): array => ['name' => $recipient->name, 'email' => $recipient->email],
        )->all();

        if ($this->rows === []) {
            $this->addRow();
        }
    }

    public function addRow(): void
    {
        $this->rows[] = ['name' => '', 'email' => ''];
    }

    public function removeRow(int $index): void
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function save(AnnouncementService $announcements): void
    {
        $announcement = Announcement::query()->findOrFail($this->announcementId);

        // Livewire actions skip route middleware, so the permission and the draft-only rule are
        // checked here again.
        abort_unless(auth()->user()?->can('announcements.edit') && $announcement->status->isEditable(), 403);

        $validated = $this->validate([
            'rows' => ['array', 'max:200'],
            'rows.*.name' => ['required', 'string', 'max:150'],
            'rows.*.email' => ['required', 'email', 'max:150', 'distinct'],
        ], attributes: [
            'rows.*.name' => __('Name'),
            'rows.*.email' => __('Email'),
        ]);

        $announcements->syncRecipients($announcement, $validated['rows']);

        $this->dispatch('toast', message: __('Recipients saved.'), type: 'success');
    }

    public function render(): View
    {
        return view('livewire.announcements.recipients-tab');
    }
}
