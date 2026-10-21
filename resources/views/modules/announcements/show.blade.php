{{-- View only: never a form here. Editing happens on the edit page, and only while a draft. --}}
<x-layouts.app :title="$announcement->title" :breadcrumbs="[__('Announcements') => route('announcements.index'), __('View') => null]">
    <x-card :title="__('Announcement details')">
        @if ($announcement->status->isEditable())
            @can('announcements.edit')
                <x-slot:actions>
                    <x-button variant="outline-primary" icon="bi-pencil" :href="route('announcements.edit', $announcement)">{{ __('Edit') }}</x-button>
                </x-slot:actions>
            @endcan
        @endif

        <dl class="row mb-0">
            <dt class="col-sm-3">{{ __('Status') }}</dt>
            <dd class="col-sm-9"><x-status-badge :status="$announcement->status" /></dd>
            <dt class="col-sm-3">{{ __('Start date') }}</dt>
            <dd class="col-sm-9">{{ $announcement->starts_on->format('d/m/Y') }}</dd>
            <dt class="col-sm-3">{{ __('End date') }}</dt>
            <dd class="col-sm-9">{{ $announcement->ends_on?->format('d/m/Y') ?? '-' }}</dd>
            <dt class="col-sm-3">{{ __('Author') }}</dt>
            <dd class="col-sm-9">{{ $announcement->author->name }}</dd>
            <dt class="col-sm-3">{{ __('Published at') }}</dt>
            <dd class="col-sm-9">{{ $announcement->published_at?->format('d/m/Y H:i') ?? '-' }}</dd>
            <dt class="col-sm-3">{{ __('Content') }}</dt>
            <dd class="col-sm-9 mb-0">{!! nl2br(e($announcement->body)) !!}</dd>
        </dl>
    </x-card>

    <x-card :title="__('Recipients')">
        @if ($announcement->recipients->isEmpty())
            <p class="text-body-secondary mb-0">{{ __('No recipients.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('No.') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcement->recipients as $recipient)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $recipient->name }}</td>
                                <td>{{ $recipient->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layouts.app>
