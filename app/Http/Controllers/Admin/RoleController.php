<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Services\Admin\RoleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

final class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index(): View
    {
        return view('modules.admin.roles.index');
    }

    public function datatable(): JsonResponse
    {
        return DataTables::eloquent($this->roles->listingQuery())
            ->addIndexColumn()
            ->addColumn('action', fn (Role $role): string => view('modules.admin.roles._partials.row-actions', [
                'role' => $role,
                'deletable' => $this->roles->deletionBlocker($role) === null,
            ])->render())
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create(): View
    {
        return view('modules.admin.roles.create', [
            'role' => new Role,
            'groups' => $this->roles->permissionGroups(),
            'granted' => [],
            'isSuperAdmin' => false,
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->roles->create($request->roleData());

        return redirect()->route('admin.roles.index')->with('success', __('Role created.'));
    }

    public function edit(Role $role): View
    {
        return view('modules.admin.roles.edit', [
            'role' => $role,
            'groups' => $this->roles->permissionGroups(),
            'granted' => $role->permissions()->pluck('name')->all(),
            'isSuperAdmin' => $this->roles->isSuperAdmin($role),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->roles->update($role, $request->roleData());

        return redirect()->route('admin.roles.index')->with('success', __('Role updated.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $blocker = $this->roles->deletionBlocker($role);
        if ($blocker !== null) {
            return back()->with('error', $blocker);
        }

        $this->roles->delete($role);

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted.'));
    }
}
