/**
 * Select2 on every <select class="js-select2">.
 *
 * Select2 only fires jQuery events, which never reach addEventListener — so code listening the
 * normal way (or wire:model) never hears the change. Re-emit a native 'change' after each pick.
 * Inside a Livewire component, wrap the select in wire:ignore so a re-render doesn't wipe Select2.
 */
export function initSelect2(root = document) {
    window.$(root).find('select.js-select2').each(function () {
        const $select = window.$(this);
        if ($select.hasClass('select2-hidden-accessible')) {
            return;
        }

        const $modal = $select.closest('.modal');
        $select.select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $modal.length ? $modal : window.$(document.body),
            placeholder: $select.data('placeholder') ?? '',
            allowClear: !$select.prop('required') && !$select.prop('multiple'),
        });

        $select.on('select2:select select2:unselect select2:clear', () => {
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
}
