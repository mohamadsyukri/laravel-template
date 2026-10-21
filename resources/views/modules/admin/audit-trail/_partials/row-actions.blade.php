@can('admin.audit-trail.show')
    <a href="{{ route('admin.audit-trail.show', $activity) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}" aria-label="{{ __('View') }}"><i class="bi bi-eye"></i></a>
@endcan
