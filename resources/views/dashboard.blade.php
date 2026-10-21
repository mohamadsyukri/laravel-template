<x-layouts.app :title="__('Dashboard')">
    <div class="row g-4">
        <div class="col-lg-5">
            <x-card>
                <h2 class="h5">{{ __('Welcome, :name', ['name' => auth()->user()->name]) }}</h2>
                <p class="text-body-secondary mb-0">{{ __('Use the menu on the left to get started.') }}</p>
            </x-card>
        </div>

        @can('announcements.index')
            <div class="col-lg-7">
                <x-card :title="__('Latest announcements')">
                    @forelse ($latestAnnouncements as $announcement)
                        <div @class(['d-flex justify-content-between gap-3 py-2', 'border-top' => ! $loop->first])>
                            <a href="{{ route('announcements.show', $announcement) }}" class="text-decoration-none">{{ $announcement->title }}</a>
                            <span class="small text-body-secondary text-nowrap">{{ $announcement->starts_on->format('d/m/Y') }}</span>
                        </div>
                    @empty
                        <p class="text-body-secondary mb-0">{{ __('No announcements yet.') }}</p>
                    @endforelse
                </x-card>
            </div>
        @endcan
    </div>
</x-layouts.app>
