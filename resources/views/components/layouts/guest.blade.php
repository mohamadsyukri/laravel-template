@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center min-vh-100 py-4">
    <main class="container">
        <div class="guest-card mx-auto">
            <div class="text-center mb-4">
                <i class="bi bi-grid-1x2-fill fs-1 text-primary"></i>
                <h1 class="h4 mt-2 mb-0">{{ config('app.name') }}</h1>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @if ($title)
                        <h2 class="h5 mb-3">{{ $title }}</h2>
                    @endif
                    {{ $slot }}
                </div>
            </div>
        </div>
    </main>
</body>
</html>
