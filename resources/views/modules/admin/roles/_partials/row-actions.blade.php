<div class="d-inline-flex gap-1">
    @can('admin.roles.edit')
        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}"><i class="bi bi-pencil"></i></a>
    @endcan
    @if ($deletable)
        @can('admin.roles.destroy')
            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}" data-confirm="{{ __('Delete this role?') }}"><i class="bi bi-trash"></i></button>
            </form>
        @endcan
    @endif
</div>
