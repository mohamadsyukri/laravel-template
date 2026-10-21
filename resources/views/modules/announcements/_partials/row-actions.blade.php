<div class="d-inline-flex gap-1">
    @can('announcements.show')
        <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}" aria-label="{{ __('View') }}"><i class="bi bi-eye"></i></a>
    @endcan
    @if ($announcement->status->isEditable())
        @can('announcements.edit')
            <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}"><i class="bi bi-pencil"></i></a>
        @endcan
        @can('announcements.destroy')
            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}" data-confirm="{{ __('Delete this announcement?') }}"><i class="bi bi-trash"></i></button>
            </form>
        @endcan
    @endif
</div>
