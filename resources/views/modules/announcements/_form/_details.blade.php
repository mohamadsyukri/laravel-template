{{-- Shared by create and edit. SIMPAN keeps a draft; TERBIT publishes and freezes it. --}}
<x-card :title="__('Announcement details')">
    <x-form.input name="title" :label="__('Title')" :value="$announcement->title" maxlength="200" required />
    <x-form.textarea name="body" :label="__('Content')" :value="$announcement->body" rows="6" required />
    <div class="row">
        <div class="col-md-6">
            <x-form.date name="starts_on" :label="__('Start date')" :value="$announcement->starts_on" required />
        </div>
        <div class="col-md-6">
            <x-form.date name="ends_on" :label="__('End date')" :value="$announcement->ends_on" />
        </div>
    </div>

    <x-slot:footer>
        <button type="submit" name="form_action" value="save" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>{{ __('Save') }}
        </button>
        <button type="submit" name="form_action" value="publish" class="btn btn-success" data-confirm="{{ __('Publish this announcement? It cannot be edited afterwards.') }}">
            <i class="bi bi-send me-1"></i>{{ __('Publish') }}
        </button>
    </x-slot:footer>
</x-card>
