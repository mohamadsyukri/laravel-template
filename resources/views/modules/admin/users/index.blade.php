<x-layouts.app :title="__('Users')" :breadcrumbs="[__('Administration') => null, __('Users') => null]">
    <x-card :title="__('User list')">
        @can('admin.users.create')
            <x-slot:actions>
                <x-button icon="bi-plus-lg" :href="route('admin.users.create')">{{ __('Add') }}</x-button>
            </x-slot:actions>
        @endcan

        <x-table id="users-table" :url="route('admin.users.datatable')" :order="[[1, 'asc']]" :columns="[
            ['data' => 'DT_RowIndex', 'title' => __('No.'), 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'title' => __('Name')],
            ['data' => 'email', 'title' => __('Email')],
            ['data' => 'roles', 'title' => __('Roles'), 'orderable' => false, 'searchable' => false],
            ['data' => 'is_active', 'title' => __('Status'), 'searchable' => false],
            ['data' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false, 'className' => 'text-end'],
        ]" />
    </x-card>
</x-layouts.app>
