<?php

declare(strict_types=1);

namespace App\Enums;

enum AnnouncementStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Published => __('Published'),
        };
    }

    /**
     * Bootstrap colour for <x-status-badge>.
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Published => 'success',
        };
    }

    /**
     * Published announcements are frozen: they may be read, not edited or deleted.
     */
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    /**
     * @return array<string, string> value => label, for filter and form selects
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])->all();
    }
}
