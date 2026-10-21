/**
 * The one confirm dialog (#confirm-modal, in the app layout). Never use window.confirm().
 *
 * Any element with data-confirm="Message" asks first, then:
 *  - with data-confirm-livewire="method": calls that method on the surrounding Livewire component;
 *  - a link: follows it;
 *  - otherwise: submits its form, keeping the pressed button's name/value (e.g. form_action).
 */
export function initConfirm() {
    const modalElement = document.getElementById('confirm-modal');
    if (!modalElement) {
        return;
    }

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    let pending = null;

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-confirm]');
        if (!trigger) {
            return;
        }

        event.preventDefault();
        pending = trigger;
        modalElement.querySelector('[data-confirm-message]').textContent = trigger.dataset.confirm;
        modal.show();
    });

    modalElement.querySelector('[data-confirm-ok]').addEventListener('click', () => {
        const trigger = pending;
        pending = null;
        modal.hide();

        if (!trigger) {
            return;
        }

        if (trigger.dataset.confirmLivewire) {
            const root = trigger.closest('[wire\\:id]');
            window.Livewire.find(root.getAttribute('wire:id')).call(trigger.dataset.confirmLivewire);
            return;
        }

        if (trigger.tagName === 'A') {
            window.location.href = trigger.href;
            return;
        }

        const form = trigger.form ?? trigger.closest('form');
        if (!form) {
            return;
        }

        // form.submit() doesn't send the clicked button, so carry its name/value across.
        if (trigger.name) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = trigger.name;
            hidden.value = trigger.value;
            form.appendChild(hidden);
        }

        form.submit();
    });
}
