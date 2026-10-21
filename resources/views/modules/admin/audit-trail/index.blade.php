<x-layouts.app :title="__('Audit trail')" :breadcrumbs="[__('Administration') => null, __('Audit trail') => null]">
    <x-card :title="__('Recorded changes')">
        <x-table id="audit-trail-table" :url="route('admin.audit-trail.datatable')" :order="[[1, 'desc']]" :columns="[
            ['data' => 'DT_RowIndex', 'title' => __('No.'), 'orderable' => false, 'searchable' => false],
            ['data' => 'created_at', 'title' => __('Date'), 'searchable' => false],
            ['data' => 'causer_name', 'title' => __('User'), 'orderable' => false, 'searchable' => false],
            ['data' => 'event', 'title' => __('Event')],
            ['data' => 'subject', 'title' => __('Record'), 'orderable' => false, 'searchable' => false],
            ['data' => 'description', 'title' => __('Description')],
            ['data' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false, 'className' => 'text-end'],
        ]" />
    </x-card>
</x-layouts.app>
