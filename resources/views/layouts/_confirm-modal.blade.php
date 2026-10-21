{{-- The only confirm dialog in the app. Trigger it with data-confirm="Message" (resources/js/components/confirm.js). --}}
<div class="modal fade" id="confirm-modal" tabindex="-1" aria-labelledby="confirm-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="confirm-modal-title">{{ __('Confirmation') }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" data-confirm-message></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" data-confirm-ok>{{ __('Yes, continue') }}</button>
            </div>
        </div>
    </div>
</div>
