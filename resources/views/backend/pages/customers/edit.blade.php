@extends('backend.layouts.app')

@section('title')
    {{ __('Customer Edit') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('Edit Customer') }}' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Edit Customer') }}</h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.dashboard') }}">
                                {{ __('Home') }}
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.customers.index') }}">
                                {{ __('Customers') }}
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">
                            {{ __('Edit Customer') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-5 py-2.5 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white">{{ __('Edit Customer') }} -
                        {{ $customer->name }}
                    </h3>
                </div>
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    @include('backend.layouts.partials.messages')
                    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="space-y-6"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Full Name') }}</label>
                                <input type="text" name="name" id="name" required value="{{ $customer->name }}"
                                    placeholder="{{ __('Enter Full Name') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="whatsapp_number"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('WhatsApp Number') }}</label>
                                <input type="text" name="whatsapp_number" id="whatsapp_number" required
                                    value="{{ $customer->whatsapp_number }}" placeholder="{{ __('Enter WhatsApp Number') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="address"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Address') }}</label>
                                <input type="text" name="address" id="address"
                                    value="{{ $customer->address }}" placeholder="{{ __('Enter Address') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="birthday"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Date of Birth') }}</label>
                                <input type="date" name="birthday" id="birthday"
                                    value="{{ $customer->birthday }}" placeholder="{{ __('Enter Date of Birth') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>

                            <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ __('Gender') }}
    </label>
    <div class="mt-2 flex items-center gap-6">
        <label class="inline-flex items-center">
            <input type="radio" name="gender" value="male"
                {{ $customer->gender === 'male' ? 'checked' : '' }}
                class="text-brand-600 border-gray-300 focus:ring-brand-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Male</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="gender" value="female"
                {{ $customer->gender === 'female' ? 'checked' : '' }}
                class="text-brand-600 border-gray-300 focus:ring-brand-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Female</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="gender" value="other"
                {{ $customer->gender === 'other' ? 'checked' : '' }}
                class="text-brand-600 border-gray-300 focus:ring-brand-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Other</span>
        </label>
    </div>
</div>


                        </div>
                        <div class="space-y-4">
                            <h3 class="block font-medium text-gray-700 dark:text-gray-400">
                                {{ __('Custom Attributes') }}
                            </h3>
                            <br>

                            <div id="attributes-container" class="space-y-3">
                                @if(isset($customer))
                                    @foreach($customer->attributes as $attr)
                                        <div class="flex items-center gap-2">
                                            <label>Attribute:</label>
                                            <input type="text" name="attributes[key][]" value="{{ $attr->key }}" placeholder="Key"
                                                class="w-1/3 border rounded px-2 py-1">
                                            <label>Value:</label>
                                            <input type="text" name="attributes[value][]" value="{{ $attr->value }}"
                                                placeholder="Value" class="w-2/3 border rounded px-2 py-1">
                                            <button type="button" class="remove-attr text-red-500">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <br>
                            <button type="button" id="add-attr" class="btn-default">
                                <i class="fa fa-plus"></i> {{ __('Add Attribute') }}
                            </button>
                        </div>

                        <div class="mt-6 flex justify-start gap-4">
                            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('admin.customers.index') }}" class="btn-default">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('add-attr').addEventListener('click', function () {
            let container = document.getElementById('attributes-container');
            let div = document.createElement('div');
            div.classList.add('flex', 'items-center', 'gap-2');
            div.innerHTML = `
            <label>Attribute:</label>
            <input type="text" name="attributes[key][]" placeholder="Key" class="w-1/3 border rounded px-2 py-1">
            <label>Value:</label>
            <input type="text" name="attributes[value][]" placeholder="Value" class="w-2/3 border rounded px-2 py-1">
            <button type="button" class="remove-attr text-red-500">
                <i class="fa fa-trash"></i>
            </button>
        `;
            container.appendChild(div);
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-attr')) {
                e.target.closest('.flex').remove();
            }
        });
    </script>
@endpush