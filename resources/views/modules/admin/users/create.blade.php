<x-layouts.app :title="__('Add user')" :breadcrumbs="[__('Administration') => null, __('Users') => route('admin.users.index'), __('Add') => null]">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        @include('modules.admin.users._form._fields')
    </form>
</x-layouts.app>
