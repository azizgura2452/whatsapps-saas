@extends('backend.layouts.app')

@section('title')
    {{ __('Customers') }} | {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div x-data="{ pageName: {{ __('Customers') }} }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                {{ __('Customers') }}
                @if (request('role'))
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-800 dark:text-white">
                        {{ ucfirst(request('role')) }}
                    </span>
                @endif
            </h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                            {{ __('Home') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Users') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Users Table -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Customers') }}</h3>

                <div class="flex items-center gap-2">
                    {{-- Free text search --}}
                    @include('backend.partials.search-form', [
                        'placeholder' => __('Search by name, number, or attribute'),
                    ])

                    {{-- Attribute filter --}}
                    <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-2" id="filter-form">
                        <select name="attribute" id="attribute"
                            class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">{{ __('Select Attribute') }}</option>
                            @foreach($attributes as $attr)
                                <option value="{{ $attr }}" {{ request('attribute') == $attr ? 'selected' : '' }}>
                                    {{ ucfirst($attr) }}
                                </option>
                            @endforeach
                        </select>

                        <select name="value" id="value"
                            class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">{{ __('Select Value') }}</option>
                            {{-- Values will be populated by AJAX --}}
                        </select>

                        <button type="submit" class="btn-primary">
                            <i class="bi bi-search"></i> {{ __('Search') }}
                        </button>
                    </form>
                </div>

                <div class="flex items-center gap-2">
                    @if (auth()->user()->can('customers.create'))
                        <button type="button" onclick="toggleImportModal()" class="btn-default">
                            <i class="bi bi-file-earmark-arrow-up mr-2"></i>
                            {{ __('Import CSV') }}
                        </button>
                        <a href="{{ route('admin.customers.create') }}" class="btn-primary">
                            <i class="bi bi-plus-circle mr-2"></i>
                            {{ __('New Customer') }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                @include('backend.layouts.partials.messages')
                <table id="dataTable" class="w-full dark:text-gray-400">
                    <thead class="bg-light text-capitalize">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th width="5%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('#') }}</th>
                            <th width="15%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Name') }}</th>
                            <th width="10%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('WhatsApp Number') }}</th>
                            <th width="20%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                {{ __('Attributes') }}
                            </th>
                            @php ld_apply_filters('user_list_page_table_header_before_action', '') @endphp
                            <th width="15%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Action') }}</th>
                            @php ld_apply_filters('user_list_page_table_header_after_action', '') @endphp
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr class="{{ $loop->index + 1 != count($customers) ?  'border-b border-gray-100 dark:border-gray-800' : '' }}">
                                <td class="px-5 py-4 sm:px-6">{{ $customers->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ ucwords($customer->name) }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $customer->whatsapp_number }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    @foreach($customer->attributes->take(2) as $attr)
                                        <span class="inline-block text-xs bg-gray-100 dark:bg-gray-700 rounded px-2 py-1 mr-1">
                                            {{ $attr->key }}: {{ $attr->value }}
                                        </span>
                                    @endforeach
                                    @if($customer->attributes->count() > 2)
                                        <span class="text-xs text-gray-500">+{{ $customer->attributes->count() - 2 }} more</span>
                                    @endif
                                </td>
                                @php ld_apply_filters('customer_list_page_table_row_before_action', '', $customer) @endphp
                                <td class="flex px-5 py-4 sm:px-6 text-center gap-1">
                                    @if (auth()->user()->can('customers.view'))
                                        <a data-tooltip-target="tooltip-chat-user-{{ $customer->id }}"
                                        class="btn-default !p-3"
                                        href="{{ route('admin.customers.chat', $customer->id) }}">
                                            <i class="bi bi-whatsapp text-sm"></i>
                                        </a>
                                        <div id="tooltip-chat-user-{{ $customer->id }}" role="tooltip"
                                            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                            {{ __('View Chat') }}
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->can('customers.view'))
                                        <a data-tooltip-target="tooltip-orders-user-{{ $customer->id }}" 
                                        class="btn-default !p-3" 
                                        href="{{ route('admin.customers.orders', $customer->id) }}">
                                            <i class="bi bi-list text-sm"></i>
                                        </a>
                                        <div id="tooltip-orders-user-{{ $customer->id }}" role="tooltip" 
                                            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                            {{ __('View Orders') }}
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->can('customers.edit'))
                                        <a data-tooltip-target="tooltip-edit-user-{{ $customer->id }}" class="btn-default !p-3" href="{{ route('admin.customers.edit', $customer->id) }}">
                                            <i class="bi bi-pencil text-sm"></i>
                                        </a>
                                        <div id="tooltip-edit-user-{{ $customer->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                            {{ __('Edit Customer') }}
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    @endif
                                    @if (auth()->user()->can('customers.delete'))
                                        <a data-modal-target="delete-modal-{{ $customer->id }}" data-modal-toggle="delete-modal-{{ $customer->id }}" data-tooltip-target="tooltip-delete-user-{{ $customer->id }}" class="btn-danger !p-3" href="javascript:void(0);">
                                            <i class="bi bi-trash text-sm"></i>
                                        </a>
                                        <div id="tooltip-delete-user-{{ $customer->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                            {{ __('Delete Customer') }}
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>

                                        <div id="delete-modal-{{ $customer->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                                            <div class="relative p-4 w-full max-w-md bg-white rounded-lg shadow-lg dark:bg-gray-700 z-60">
                                                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal-{{ $customer->id }}">
                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    <span class="sr-only">{{ __('Close modal') }}</span>
                                                </button>
                                                <div class="p-4 md:p-5 text-center">
                                                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                    </svg>
                                                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('Are you sure you want to delete this user?') }}</h3>
                                                    <form id="delete-form-{{ $customer->id }}" action="{{ route('admin.users.destroy', $customer->id) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf

                                                        <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                            {{ __('Yes, Confirm') }}
                                                        </button>
                                                        <button data-modal-hide="delete-modal-{{ $customer->id }}" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ __('No, cancel') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                @php ld_apply_filters('customers_list_page_table_row_after_action', '', $customer) @endphp
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('No customers found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 w-[95%] max-w-2xl rounded-xl shadow-xl p-6 relative">
        <button onclick="toggleImportModal()"
            class="absolute top-2 right-3 text-gray-500 hover:text-red-600 text-2xl">&times;</button>

        <h2 class="text-lg font-semibold mb-4">{{ __('Import Customers from CSV') }}</h2>

        <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ __('Upload a CSV file with customer data. Required column: phone, phone_number, or whatsapp_number. Optional: name, email, and any custom attributes.') }}
                </p>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-2">{{ __('CSV File') }}</label>
                    <input type="file" name="customer_csv" accept=".csv,.txt" required
                           class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800">
                    <small class="text-gray-500 dark:text-gray-400">
                        {{ __('Max file size: 10MB') }}
                    </small>
                </div>

                <div class="bg-white dark:bg-gray-800 p-3 rounded border">
                    <p class="text-xs font-semibold mb-2">{{ __('CSV Format Example:') }}</p>
                    <pre class="text-xs bg-gray-100 dark:bg-gray-900 p-2 rounded overflow-x-auto">phone,name,email,city,age
+1234567890,John Doe,john@example.com,New York,30
+0987654321,Jane Smith,jane@example.com,Los Angeles,25</pre>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('admin.customers.download-template') }}" class="btn-default">
                    <i class="bi bi-download mr-2"></i>
                    {{ __('Download Template') }}
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="toggleImportModal()" class="btn-default">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-upload mr-2"></i>
                        {{ __('Import Customers') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.toggle('hidden');
}

document.getElementById('attribute').addEventListener('change', function() {
    let attribute = this.value;
    let valueDropdown = document.getElementById('value');

    valueDropdown.innerHTML = '<option value="">{{ __('Select Value') }}</option>';

    if (attribute) {
        fetch(`/admin/customers/attribute-values/${attribute}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(val => {
                    let option = document.createElement('option');
                    option.value = val;
                    option.textContent = val;
                    if ("{{ request('value') }}" === val) {
                        option.selected = true;
                    }
                    valueDropdown.appendChild(option);
                });
            })
            .catch(err => console.error('Error fetching attribute values:', err));
    }
});

window.addEventListener('DOMContentLoaded', function() {
    let selectedAttr = document.getElementById('attribute').value;
    if (selectedAttr) {
        document.getElementById('attribute').dispatchEvent(new Event('change'));
    }
});
</script>
@endpush