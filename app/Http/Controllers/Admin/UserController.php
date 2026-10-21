<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Blade;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

final class UserController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function index(): View
    {
        return view('modules.admin.users.index');
    }

    public function datatable(): JsonResponse
    {
        return DataTables::eloquent($this->users->listingQuery())
            ->addIndexColumn()
            ->addColumn('roles', fn (User $user): string => e($user->roles->pluck('name')->implode(', ')))
            ->editColumn('is_active', fn (User $user): string => Blade::render(
                '<span class="badge text-bg-{{ $active ? \'success\' : \'secondary\' }}">{{ $active ? __(\'Active\') : __(\'Inactive\') }}</span>',
                ['active' => $user->is_active],
            ))
            ->addColumn('action', fn (User $user): string => view('modules.admin.users._partials.row-actions', ['user' => $user])->render())
            ->rawColumns(['is_active', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        return view('modules.admin.users.create', [
            'user' => new User(['is_active' => true]),
            'roleOptions' => $this->roleOptions(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->users->create($request->userData());

        return redirect()->route('admin.users.index')->with('success', __('User created.'));
    }

    public function edit(User $user): View
    {
        return view('modules.admin.users.edit', [
            'user' => $user->load('roles'),
            'roleOptions' => $this->roleOptions(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update($user, $request->userData());

        return redirect()->route('admin.users.index')->with('success', __('User updated.'));
    }

    /**
     * @return array<string, string> role name => role name
     */
    private function roleOptions(): array
    {
        return Role::query()->orderBy('name')->pluck('name', 'name')->all();
    }
}
