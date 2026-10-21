{{-- Session flashes, shown on load by resources/js/components/toast.js (window.toast adds more). --}}
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3">
    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="toast align-items-center text-bg-{{ $type }} border-0" role="status" aria-live="polite">
                <div class="d-flex">
                    <div class="toast-body">{{ session($key) }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="{{ __('Close') }}"></button>
                </div>
            </div>
        @endif
    @endforeach
</div>
