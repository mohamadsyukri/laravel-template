<?php

declare(strict_types=1);

namespace App\Services\Announcements;

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final readonly class AnnouncementService
{
    public const string SAVE = 'save';

    public const string PUBLISH = 'publish';

    /**
     * The listing, its Excel export and its PDF all read this one query, so they always agree.
     *
     * @param  array{filter_status?: ?string, filter_from?: ?string, filter_to?: ?string}  $filters
     * @return Builder<Announcement>
     */
    public function listingQuery(array $filters): Builder
    {
        return Announcement::query()
            ->with('author:id,name')
            ->when($filters['filter_status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['filter_from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('starts_on', '>=', $from))
            ->when($filters['filter_to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('starts_on', '<=', $to));
    }

    /**
     * @param  array{title: string, body: string, starts_on: string, ends_on: ?string}  $data
     */
    public function create(array $data, string $formAction, User $author): Announcement
    {
        return Announcement::query()->create([...$data, 'created_by' => $author->id, ...$this->statusFor($formAction)]);
    }

    /**
     * @param  array{title: string, body: string, starts_on: string, ends_on: ?string}  $data
     */
    public function update(Announcement $announcement, array $data, string $formAction): Announcement
    {
        $announcement->update([...$data, ...$this->statusFor($formAction)]);

        return $announcement;
    }

    public function delete(Announcement $announcement): void
    {
        $announcement->delete();
    }

    /**
     * @param  list<array{name: string, email: string}>  $rows
     */
    public function syncRecipients(Announcement $announcement, array $rows): void
    {
        // Replace-style write in one transaction: if an insert fails, the old list survives.
        DB::transaction(function () use ($announcement, $rows): void {
            $announcement->recipients()->delete();
            $announcement->recipients()->createMany($rows);
        });
    }

    /**
     * SIMPAN keeps a draft; TERBIT publishes it, which also freezes it (AnnouncementStatus::isEditable).
     *
     * @return array{status: AnnouncementStatus, published_at?: Carbon}
     */
    private function statusFor(string $formAction): array
    {
        return match ($formAction) {
            self::SAVE => ['status' => AnnouncementStatus::Draft],
            self::PUBLISH => ['status' => AnnouncementStatus::Published, 'published_at' => now()],
        };
    }
}
