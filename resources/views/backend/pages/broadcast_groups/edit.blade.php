@extends('backend.layouts.app')

@section('title', __('Edit Broadcast Group'))

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <h2 class="text-xl font-semibold mb-4">{{ __('Edit Broadcast Group') }}</h2>

    <form action="{{ route('admin.broadcast-groups.update', $group->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Group Name --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">{{ __('Group Name') }}</label>
            <input type="text" name="name" value="{{ $group->name }}" required class="w-full border rounded px-3 py-2">
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">{{ __('Description') }}</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ $group->description }}</textarea>
        </div>

        {{-- Current Customers Count --}}
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium flex items-center">
                        <i class="bi bi-people mr-2"></i>
                        {{ __('Current Customers') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Total customers in this group: :count', ['count' => $group->getCustomerCount()]) }}
                    </p>
                </div>
                <button type="button" onclick="showCustomers()" class="btn-default">
                    <i class="bi bi-eye mr-1"></i>
                    {{ __('View Customers') }}
                </button>
            </div>
        </div>

        {{-- Import More Customers --}}
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <h3 class="font-medium mb-3 flex items-center">
                <i class="bi bi-file-earmark-arrow-up mr-2"></i>
                {{ __('Import Additional Customers') }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                {{ __('Upload a CSV file to add more customers to this group.') }}
            </p>
            
            <div class="mb-3">
                <label class="block text-sm font-medium mb-2">{{ __('CSV File') }}</label>
                <input type="file" name="customer_csv" accept=".csv,.txt" 
                       class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800">
            </div>
        </div>

        {{-- Conditions --}}
        <h3 class="font-medium mb-2">{{ __('Filter Conditions') }}</h3>
        <div id="conditions-container" class="space-y-2"></div>

        <button type="button" id="add-condition" class="btn-default mt-2">
            <i class="fa fa-plus"></i> {{ __('Add Condition') }}
        </button>

        {{-- Form Actions --}}
        <div class="mt-6">
            <button type="submit" class="btn-primary">{{ __('Update') }}</button>
            <a href="{{ route('admin.broadcast-groups.index') }}" class="btn-default">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

{{-- Customers Modal --}}
<div id="customersModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 w-[95%] max-w-2xl rounded-xl shadow-xl p-6 relative max-h-[80vh] overflow-y-auto">
        <button onclick="closeCustomers()"
            class="absolute top-2 right-3 text-gray-500 hover:text-red-600 text-2xl">&times;</button>

        <h2 class="text-lg font-semibold mb-4">{{ __('Customers in this Group') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-2 text-left border-b">#</th>
                        <th class="p-2 text-left border-b">{{ __('Name') }}</th>
                        <th class="p-2 text-left border-b">{{ __('Phone') }}</th>
                        <th class="p-2 text-left border-b">{{ __('Email') }}</th>
                    </tr>
                </thead>
                <tbody id="customersTableBody">
                    @foreach($group->customers as $customer)
                    <tr class="border-t">
                        <td class="p-2">{{ $loop->iteration }}</td>
                        <td class="p-2">{{ $customer->name ?? '-' }}</td>
                        <td class="p-2">{{ $customer->phone }}</td>
                        <td class="p-2">{{ $customer->email ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let attributes = @json($attributes);
let existingConditions = @json($group->conditions ?? []);

function showCustomers() {
    document.getElementById('customersModal').classList.remove('hidden');
}

function closeCustomers() {
    document.getElementById('customersModal').classList.add('hidden');
}

function buildConditionRow(selectedField = '', selectedOperator = '=', selectedValue = '') {
    let options = attributes.map(attr => {
        return `<option value="${attr}" ${attr === selectedField ? 'selected' : ''}>${attr}</option>`;
    }).join('');

    return `
        <div class="flex items-center gap-2 condition-row">
            <select name="conditions[field][]" class="w-1/3 border rounded px-2 py-1 attr-select" style="min-width: 150px;">
                <option value="">{{ __('Select Attribute') }}</option>
                ${options}
            </select>

            <select name="conditions[operator][]" class="border rounded px-2 py-1">
                <option value="=" ${selectedOperator === '=' ? 'selected' : ''}>=</option>
                <option value="!=" ${selectedOperator === '!=' ? 'selected' : ''}>!=</option>
                <option value="LIKE" ${selectedOperator === 'LIKE' ? 'selected' : ''}>LIKE</option>
                <option value="IN" ${selectedOperator === 'IN' ? 'selected' : ''}>IN</option>
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

// Render existing conditions on load
document.addEventListener('DOMContentLoaded', function () {
    if(existingConditions.length > 0){
        existingConditions.forEach(cond => {
            document.getElementById('conditions-container').insertAdjacentHTML(
                'beforeend',
                buildConditionRow(cond.field, cond.operator, cond.value)
            );

            if(cond.field){
                let lastRow = document.querySelector('#conditions-container .condition-row:last-child');
                let valSelect = lastRow.querySelector('.val-select');

                fetch(`{{ url('/admin/customers/attribute-values') }}/${cond.field}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.length > 0){
                            data.forEach(v => {
                                let opt = document.createElement('option');
                                opt.value = v;
                                opt.textContent = v;
                                if(v === cond.value) opt.selected = true;
                                valSelect.appendChild(opt);
                            });
                        } else {
                            let opt = document.createElement('option');
                            opt.value = '';
                            opt.textContent = '{{ __("No predefined values – type manually") }}';
                            valSelect.appendChild(opt);

                            valSelect.insertAdjacentHTML('afterend', 
                                `<input type="text" name="conditions[value][]" 
                                        value="${cond.value ?? ''}"
                                        class="w-1/3 border rounded px-2 py-1 mt-2 val-input" 
                                        placeholder="{{ __("Enter value manually") }}">`
                            );
                        }
                    });
            }
        });
    }
});

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

// Populate values when attribute changes
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