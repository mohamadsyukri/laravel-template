<x-layouts.app :title="__('Roles')" :breadcrumbs="[__('Administration') => null, __('Roles') => null]">
    <x-card :title="__('Role list')">
        @can('admin.roles.create')
            <x-slot:actions>
                <x-button icon="bi-plus-lg" :href="route('admin.roles.create')">{{ __('Add') }}</x-button>
            </x-slot:actions>
        @endcan

        <x-table id="roles-table" :url="route('admin.roles.datatable')" :order="[[1, 'asc']]" :columns="[
            ['data' => 'DT_RowIndex', 'title' => __('No.'), 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'title' => __('Role name')],
            ['data' => 'users_count', 'title' => __('Users'), 'searchable' => false],
            ['data' => 'permissions_count', 'title' => __('Permissions'), 'searchable' => false],
            ['data' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false, 'className' => 'text-end'],
        ]" />
    </x-card>
</x-layouts.app>
