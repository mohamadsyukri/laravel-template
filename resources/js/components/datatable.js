/**
 * Server-side DataTables for every <table data-datatable> (rendered by the x-table component),
 * fed by a yajra endpoint. Filters live in a separate form named by data-filters; its fields are
 * sent with every request and the table reloads when the form is submitted or reset.
 */
export function initDataTables(root = document) {
    root.querySelectorAll('table[data-datatable]:not([data-initialised])').forEach((table) => {
        table.dataset.initialised = '1';

        const filterForm = table.dataset.filters ? document.querySelector(table.dataset.filters) : null;

        const dataTable = new window.DataTable(table, {
            serverSide: true,
            processing: true,
            ajax: {
                url: table.dataset.url,
                data: (params) => {
                    if (filterForm) {
                        new FormData(filterForm).forEach((value, key) => {
                            params[key] = value;
                        });
                    }
                },
            },
            columns: JSON.parse(table.dataset.columns),
            order: JSON.parse(table.dataset.order || '[]'),
            language: window.dataTableLanguage ?? {},
            pageLength: 10,
        });

        if (filterForm) {
            filterForm.addEventListener('submit', (event) => {
                event.preventDefault();
                dataTable.ajax.reload();
            });
            filterForm.addEventListener('reset', () => setTimeout(() => dataTable.ajax.reload(), 0));
        }
    });
}

/**
 * Export/print links with data-with-filters="#filter-form" carry that form's current values, so
 * the downloaded file matches what the table shows.
 */
export function initFilteredLinks() {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[data-with-filters]');
        const form = link ? document.querySelector(link.dataset.withFilters) : null;
        if (!form) {
            return;
        }

        link.dataset.baseHref ??= link.href;
        const url = new URL(link.dataset.baseHref);
        new FormData(form).forEach((value, key) => {
            if (value !== '') {
                url.searchParams.set(key, value);
            }
        });
        link.href = url.toString();
    });
}
