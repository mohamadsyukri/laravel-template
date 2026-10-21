{{--
    A server-side DataTable (resources/js/components/datatable.js) fed by a yajra endpoint.
    columns: list of ['data' => ..., 'title' => ..., 'orderable' => bool, 'searchable' => bool, 'className' => ...]
    filters: CSS selector of the filter form whose fields go with every request.
--}}
@props([
    'id',
    'url',
    'columns',
    'filters' => null,
    'order' => [],
])
@php
    $dataTableColumns = collect($columns)->map(fn (array $column): array => array_filter([
        'data' => $column['data'],
        'name' => $column['name'] ?? $column['data'],
        'orderable' => $column['orderable'] ?? true,
        'searchable' => $column['searchable'] ?? true,
        'className' => $column['className'] ?? null,
    ], fn (mixed $value): bool => $value !== null))->values();
@endphp
<div class="table-responsive">
    <table id="{{ $id }}" class="table table-hover align-middle w-100"
        data-datatable
        data-url="{{ $url }}"
        data-columns='@json($dataTableColumns)'
        data-order='@json($order)'
        @if ($filters) data-filters="{{ $filters }}" @endif>
        <thead class="table-light">
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column['title'] }}</th>
                @endforeach
            </tr>
        </thead>
    </table>
</div>
