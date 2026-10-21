<x-layouts.app :title="__('Add announcement')" :breadcrumbs="[__('Announcements') => route('announcements.index'), __('Add') => null]">
    <form method="POST" action="{{ route('announcements.store') }}">
        @csrf
        @include('modules.announcements._form._details')
    </form>
    <p class="text-body-secondary small">{{ __('Recipients can be added after the announcement is saved.') }}</p>
</x-layouts.app>
