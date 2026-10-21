@props([
    'title' => null,
    'breadcrumbs' => [],
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="d-lg-flex">
        @include('layouts._sidebar')

        <div class="app-main flex-grow-1">
            @include('layouts._topbar')

            <main class="container-fluid py-4">
                @if ($title || $breadcrumbs)
                    <div class="mb-3">
                        <x-breadcrumb :items="$breadcrumbs" />
                        @if ($title)
                            <h1 class="h4 mb-0">{{ $title }}</h1>
                        @endif
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @include('layouts._toasts')
    @include('layouts._confirm-modal')

    @php($dataTableLanguage = trans('datatables'))
    <script>
        window.dataTableLanguage = @json(is_array($dataTableLanguage) ? $dataTableLanguage : []);
    </script>
    @livewireScripts
</body>
</html>
