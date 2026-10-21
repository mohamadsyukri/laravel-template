{{-- Both tabs are in the page, so switching is instant; each tab saves on its own. --}}
<x-layouts.app :title="__('Edit announcement')" :breadcrumbs="[__('Announcements') => route('announcements.index'), __('Edit') => null]">
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-details-button" data-bs-toggle="tab" data-bs-target="#tab-details" type="button" role="tab" aria-controls="tab-details" aria-selected="true">{{ __('Details') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-recipients-button" data-bs-toggle="tab" data-bs-target="#tab-recipients" type="button" role="tab" aria-controls="tab-recipients" aria-selected="false">{{ __('Recipients') }}</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-details" role="tabpanel" aria-labelledby="tab-details-button">
            <form method="POST" action="{{ route('announcements.update', $announcement) }}">
                @csrf
                @method('PUT')
                @include('modules.announcements._form._details')
            </form>
        </div>
        <div class="tab-pane fade" id="tab-recipients" role="tabpanel" aria-labelledby="tab-recipients-button">
            <livewire:announcements.recipients-tab :announcement="$announcement" />
        </div>
    </div>
</x-layouts.app>
