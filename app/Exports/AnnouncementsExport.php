<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Excel export of the announcement listing, with the same filters the screen had.
 *
 * @implements WithMapping<Announcement>
 */
final class AnnouncementsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  Builder<Announcement>  $query
     */
    public function __construct(private readonly Builder $query) {}

    /**
     * @return Builder<Announcement>
     */
    public function query(): Builder
    {
        return $this->query->orderBy('starts_on');
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [__('Title'), __('Status'), __('Start date'), __('End date'), __('Author')];
    }

    /**
     * @param  Announcement  $announcement
     * @return list<?string>
     */
    public function map($announcement): array
    {
        return [
            $announcement->title,
            $announcement->status->label(),
            $announcement->starts_on->format('d/m/Y'),
            $announcement->ends_on?->format('d/m/Y'),
            $announcement->author->name,
        ];
    }
}
