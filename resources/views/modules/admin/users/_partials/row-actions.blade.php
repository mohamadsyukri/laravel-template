@can('admin.users.edit')
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}"><i class="bi bi-pencil"></i></a>
@endcan
