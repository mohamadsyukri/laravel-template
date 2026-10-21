<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AnnouncementRecipientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['announcement_id', 'name', 'email'])]
final class AnnouncementRecipient extends Model
{
    /** @use HasFactory<AnnouncementRecipientFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Announcement, $this>
     */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }
}
