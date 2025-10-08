@extends('backend.layouts.app')

@section('title', __('New Broadcast Group'))

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <h2 class="text-xl font-semibold mb-4">{{ __('Create Broadcast Group') }}</h2>

    <form action="{{ route('admin.broadcast-groups.store') }}" method="POST">
        @csrf

        {{-- Group Name --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">{{ __('Group Name') }}</label>
            <input type="text" name="name" required class="w-full border rounded px-3 py-2">
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">{{ __('Description') }}</label>
            <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        {{-- Conditions --}}
        <h3 class="font-medium mb-2">{{ __('Conditions') }}</h3>
        <div id="conditions-container" class="space-y-2"></div>

        <button type="button" id="add-condition" class="btn-default mt-2">
            <i class="fa fa-plus"></i> {{ __('Add Condition') }}
        </button>

        {{-- Form Actions --}}
        <div class="mt-6">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            <a href="{{ route('admin.broadcast-groups.index') }}" class="btn-default">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let attributes = @json($attributes);

function buildConditionRow() {
    let options = attributes.map(attr => `<option value="${attr}">${attr}</option>`).join('');

    return `
        <div class="flex items-center gap-2 condition-row">
            <select name="conditions[field][]" class="w-1/3 border rounded px-2 py-1 attr-select" style="min-width: 150px;">
                <option value="">{{ __('Select Attribute') }}</option>
                ${options}
            </select>

            <select name="conditions[operator][]" class="border rounded px-2 py-1">
                <option value="=">=</option>
                <option value="!=">!=</option>
                <option value="LIKE">LIKE</option>
                <option value="IN">IN</option>
            </select>

            <select name="conditions[value][]" class="w-1/3 border rounded px-3 py-1 val-select" style="min-width: 150px;">
                <option value="">{{ __('Select Value') }}</option>
            </select>

            <button type="button" class="remove-cond text-red-500">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    `;
}

// Add condition row
document.getElementById('add-condition').addEventListener('click', function () {
    document.getElementById('conditions-container').insertAdjacentHTML('beforeend', buildConditionRow());
});

// Remove condition row
document.addEventListener('click', function(e){
    if(e.target.closest('.remove-cond')){
        e.target.closest('.condition-row').remove();
    }
});

// Populate values based on attribute selection
document.addEventListener('change', function(e){
    if(e.target.classList.contains('attr-select')){
        let attr = e.target.value;
        let valSelect = e.target.parentElement.querySelector('.val-select');
        valSelect.innerHTML = '<option value="">{{ __('Select Value') }}</option>';

        if(attr){
            fetch(`{{ url('/admin/customers/attribute-values') }}/${attr}`)
                .then(res => res.json())
                .then(data => {
                    if(data.length > 0){
                        data.forEach(v => {
                            let opt = document.createElement('option');
                            opt.value = v;
                            opt.textContent = v;
                            valSelect.appendChild(opt);
                        });
                    } else {
                        // if no predefined values, allow free text
                        let opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = '{{ __("No predefined values – type manually") }}';
                        valSelect.appendChild(opt);
                        valSelect.insertAdjacentHTML('afterend', 
                            `<input type="text" name="conditions[value][]" 
                                    class="w-1/3 border rounded px-2 py-1 mt-2 val-input" 
                                    placeholder="{{ __("Enter value manually") }}">`
                        );
                    }
                });
        }
    }
});
</script>
@endpush
