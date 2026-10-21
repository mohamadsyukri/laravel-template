<x-layouts.app :title="__('Edit user')" :breadcrumbs="[__('Administration') => null, __('Users') => route('admin.users.index'), __('Edit') => null]">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('modules.admin.users._form._fields')
    </form>
</x-layouts.app>
