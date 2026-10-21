{{-- Any enum with label() and color(), e.g. App\Enums\AnnouncementStatus. --}}
@props(['status'])
<span class="badge text-bg-{{ $status->color() }}">{{ $status->label() }}</span>
