<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

/**
 * Read-only view of the activity log: who changed what, and the values before and after.
 */
final class AuditTrailController extends Controller
{
    public function index(): View
    {
        return view('modules.admin.audit-trail.index');
    }

    public function datatable(): JsonResponse
    {
        return DataTables::eloquent(Activity::query()->with('causer'))
            ->addIndexColumn()
            ->editColumn('created_at', fn (Activity $activity): string => $activity->created_at->format('d/m/Y H:i'))
            ->addColumn('causer_name', fn (Activity $activity): string => e($activity->causer?->name ?? __('System')))
            ->addColumn('subject', fn (Activity $activity): string => e(class_basename((string) $activity->subject_type).' #'.$activity->subject_id))
            ->editColumn('event', fn (Activity $activity): string => e(__(ucfirst((string) $activity->event))))
            ->addColumn('action', fn (Activity $activity): string => view('modules.admin.audit-trail._partials.row-actions', ['activity' => $activity])->render())
            ->rawColumns(['action'])
            ->toJson();
    }

    public function show(Activity $activity): View
    {
        $changes = $activity->attribute_changes;

        return view('modules.admin.audit-trail.show', [
            'activity' => $activity->load('causer'),
            'new' => $changes?->get('attributes', []) ?? [],
            'old' => $changes?->get('old', []) ?? [],
        ]);
    }
}
