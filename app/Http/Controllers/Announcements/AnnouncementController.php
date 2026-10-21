<?php

declare(strict_types=1);

namespace App\Http\Controllers\Announcements;

use App\Enums\AnnouncementStatus;
use App\Exports\AnnouncementsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Announcements\StoreAnnouncementRequest;
use App\Http\Requests\Announcements\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Services\Announcements\AnnouncementService;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

/**
 * The reference module: copy this shape for new features (thin controller, service holds logic).
 */
final class AnnouncementController extends Controller
{
    private const array FILTERS = ['filter_status', 'filter_from', 'filter_to'];

    public function __construct(private readonly AnnouncementService $announcements) {}

    public function index(): View
    {
        return view('modules.announcements.index', ['statusOptions' => AnnouncementStatus::options()]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->announcements->listingQuery($request->only(self::FILTERS)))
            ->addIndexColumn()
            ->editColumn('starts_on', fn (Announcement $announcement): string => $announcement->starts_on->format('d/m/Y'))
            ->editColumn('status', fn (Announcement $announcement): string => Blade::render('<x-status-badge :status="$status" />', ['status' => $announcement->status]))
            ->addColumn('author', fn (Announcement $announcement): string => e($announcement->author->name))
            ->addColumn('action', fn (Announcement $announcement): string => view('modules.announcements._partials.row-actions', ['announcement' => $announcement])->render())
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function export(Request $request): BinaryFileResponse
    {
        $query = $this->announcements->listingQuery($request->only(self::FILTERS));

        return Excel::download(new AnnouncementsExport($query), 'pengumuman-'.now()->format('Ymd').'.xlsx');
    }

    public function pdf(Request $request): Response
    {
        $announcements = $this->announcements->listingQuery($request->only(self::FILTERS))->orderBy('starts_on')->get();

        return SnappyPdf::loadView('modules.announcements.pdf', ['announcements' => $announcements])
            ->setPaper('a4', 'landscape')
            ->download('pengumuman-'.now()->format('Ymd').'.pdf');
    }

    public function create(): View
    {
        return view('modules.announcements.create', [
            'announcement' => new Announcement(['starts_on' => now()->toDateString()]),
        ]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $announcement = $this->announcements->create($request->announcementData(), $request->formAction(), $request->user());

        return $this->afterSave($announcement);
    }

    public function show(Announcement $announcement): View
    {
        return view('modules.announcements.show', ['announcement' => $announcement->load(['author', 'recipients'])]);
    }

    public function edit(Announcement $announcement): View
    {
        abort_unless($announcement->status->isEditable(), 403);

        return view('modules.announcements.edit', ['announcement' => $announcement]);
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->announcements->update($announcement, $request->announcementData(), $request->formAction());

        return $this->afterSave($announcement);
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        abort_unless($announcement->status->isEditable(), 403);

        $this->announcements->delete($announcement);

        return redirect()->route('announcements.index')->with('success', __('Announcement deleted.'));
    }

    /**
     * A draft goes back to its form (the recipients tab is there); a published one is read-only.
     */
    private function afterSave(Announcement $announcement): RedirectResponse
    {
        return $announcement->status->isEditable()
            ? redirect()->route('announcements.edit', $announcement)->with('success', __('Announcement saved.'))
            : redirect()->route('announcements.show', $announcement)->with('success', __('Announcement published.'));
    }
}
