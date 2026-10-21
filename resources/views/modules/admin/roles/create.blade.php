<x-layouts.app :title="__('Add role')" :breadcrumbs="[__('Administration') => null, __('Roles') => route('admin.roles.index'), __('Add') => null]">
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        @include('modules.admin.roles._form._fields')
    </form>
</x-layouts.app>
