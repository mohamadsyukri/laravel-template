<?php

declare(strict_types=1);

namespace App\Http\Requests\Announcements;

use App\Models\Announcement;

final class UpdateAnnouncementRequest extends AnnouncementFormRequest
{
    /**
     * A published announcement is frozen, whatever the user's permissions.
     */
    public function authorize(): bool
    {
        /** @var Announcement $announcement */
        $announcement = $this->route('announcement');

        return $announcement->status->isEditable();
    }
}
