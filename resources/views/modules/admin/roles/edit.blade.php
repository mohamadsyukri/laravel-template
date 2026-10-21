<x-layouts.app :title="__('Edit role')" :breadcrumbs="[__('Administration') => null, __('Roles') => route('admin.roles.index'), __('Edit') => null]">
    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')
        @include('modules.admin.roles._form._fields')
    </form>
</x-layouts.app>
