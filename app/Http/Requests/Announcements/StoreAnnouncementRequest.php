<?php

declare(strict_types=1);

namespace App\Http\Requests\Announcements;

final class StoreAnnouncementRequest extends AnnouncementFormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
