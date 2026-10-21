<x-layouts.app :title="__('Change details')" :breadcrumbs="[__('Administration') => null, __('Audit trail') => route('admin.audit-trail.index'), __('View') => null]">
    <x-card :title="__('Change details')">
        <dl class="row mb-0">
            <dt class="col-sm-3">{{ __('Date') }}</dt>
            <dd class="col-sm-9">{{ $activity->created_at->format('d/m/Y H:i:s') }}</dd>
            <dt class="col-sm-3">{{ __('User') }}</dt>
            <dd class="col-sm-9">{{ $activity->causer?->name ?? __('System') }}</dd>
            <dt class="col-sm-3">{{ __('Event') }}</dt>
            <dd class="col-sm-9">{{ __(ucfirst((string) $activity->event)) }}</dd>
            <dt class="col-sm-3">{{ __('Record') }}</dt>
            <dd class="col-sm-9">{{ class_basename((string) $activity->subject_type) }} #{{ $activity->subject_id }}</dd>
            <dt class="col-sm-3">{{ __('Description') }}</dt>
            <dd class="col-sm-9 mb-0">{{ $activity->description }}</dd>
        </dl>
    </x-card>

    <x-card :title="__('Values')">
        @php($fields = array_unique([...array_keys($old), ...array_keys($new)]))
        @if ($fields === [])
            <p class="text-body-secondary mb-0">{{ __('No values were recorded for this change.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Field') }}</th>
                            <th>{{ __('Before') }}</th>
                            <th>{{ __('After') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fields as $field)
                            <tr>
                                <td class="text-nowrap"><code>{{ $field }}</code></td>
                                <td>{{ is_scalar($old[$field] ?? null) ? $old[$field] : json_encode($old[$field] ?? null) }}</td>
                                <td>{{ is_scalar($new[$field] ?? null) ? $new[$field] : json_encode($new[$field] ?? null) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layouts.app>
