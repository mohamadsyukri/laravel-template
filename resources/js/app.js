import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';
import DataTable from 'datatables.net-bs5';
import select2 from 'select2';

import { initConfirm } from './components/confirm';
import { initDataTables, initFilteredLinks } from './components/datatable';
import { initSelect2 } from './components/select2';
import { showFlashToasts, toast } from './components/toast';

// Globals for page scripts and jQuery plugins. Livewire brings its own Alpine, so don't add one.
window.bootstrap = bootstrap;
window.$ = window.jQuery = jQuery;
window.DataTable = DataTable;
window.toast = toast;
select2(window, jQuery);

document.addEventListener('DOMContentLoaded', () => {
    initConfirm();
    initDataTables();
    initFilteredLinks();
    initSelect2();
    showFlashToasts();
});

// Livewire components report results with $this->dispatch('toast', message: ..., type: ...).
window.addEventListener('toast', (event) => toast(event.detail.message, event.detail.type));
