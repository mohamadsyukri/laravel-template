/**
 * Bootstrap toasts in #toast-container: window.toast('Saved', 'success' | 'danger' | 'warning' | 'info').
 * Session flashes are rendered by the layout with the same markup and shown on page load.
 */
export function toast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) {
        return;
    }

    const element = document.createElement('div');
    element.className = `toast align-items-center text-bg-${type} border-0`;
    element.setAttribute('role', 'status');
    element.setAttribute('aria-live', 'polite');
    element.innerHTML = '<div class="d-flex"><div class="toast-body"></div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    element.querySelector('.toast-body').textContent = message;
    container.appendChild(element);

    show(element);
}

export function showFlashToasts() {
    document.querySelectorAll('#toast-container .toast').forEach(show);
}

function show(element) {
    element.addEventListener('hidden.bs.toast', () => element.remove());
    window.bootstrap.Toast.getOrCreateInstance(element, { delay: 4000 }).show();
}
