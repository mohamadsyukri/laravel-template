<x-layouts.app :title="__('Announcements')" :breadcrumbs="[__('Announcements') => null]">
    <x-card :title="__('Announcement list')">
        <x-slot:actions>
            <x-button variant="outline-secondary" icon="bi-file-earmark-excel" :href="route('announcements.export')" data-with-filters="#announcement-filters">{{ __('Excel') }}</x-button>
            <x-button variant="outline-secondary" icon="bi-printer" :href="route('announcements.pdf')" data-with-filters="#announcement-filters">{{ __('Print') }}</x-button>
            @can('announcements.create')
                <x-button icon="bi-plus-lg" :href="route('announcements.create')">{{ __('Add') }}</x-button>
            @endcan
        </x-slot:actions>

        <form id="announcement-filters" class="row g-2 align-items-end">
            <div class="col-md-3">
                <x-form.select name="filter_status" :label="__('Status')" :options="$statusOptions" :placeholder="__('All')" />
            </div>
            <div class="col-md-3">
                <x-form.date name="filter_from" :label="__('Start date from')" />
            </div>
            <div class="col-md-3">
                <x-form.date name="filter_to" :label="__('Start date to')" />
            </div>
            <div class="col-md-3 d-flex gap-2 mb-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>{{ __('Search') }}</button>
                <button type="reset" class="btn btn-outline-secondary">{{ __('Reset') }}</button>
            </div>
        </form>

        <x-table id="announcements-table" :url="route('announcements.datatable')" filters="#announcement-filters" :order="[[3, 'desc']]" :columns="[
            ['data' => 'DT_RowIndex', 'title' => __('No.'), 'orderable' => false, 'searchable' => false],
            ['data' => 'title', 'title' => __('Title')],
            ['data' => 'status', 'title' => __('Status'), 'searchable' => false],
            ['data' => 'starts_on', 'title' => __('Start date'), 'searchable' => false],
            ['data' => 'author', 'title' => __('Author'), 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false, 'className' => 'text-end'],
        ]" />
    </x-card>
</x-layouts.app>
