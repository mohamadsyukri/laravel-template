<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Announcement list') }}</title>
    {{-- wkhtmltopdf can't load Vite assets, so a PDF view is the one place styles live inline. --}}
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #212529; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #6c757d; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dee2e6; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f1f3f5; }
    </style>
</head>
<body>
    <h1>{{ __('Announcement list') }}</h1>
    <div class="meta">{{ __('Printed on :date', ['date' => now()->format('d/m/Y H:i')]) }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('No.') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Start date') }}</th>
                <th>{{ __('End date') }}</th>
                <th>{{ __('Author') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($announcements as $announcement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td>{{ $announcement->status->label() }}</td>
                    <td>{{ $announcement->starts_on->format('d/m/Y') }}</td>
                    <td>{{ $announcement->ends_on?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $announcement->author->name }}</td>
                </tr>
            @empty
                <tr><td colspan="6">{{ __('No records.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
