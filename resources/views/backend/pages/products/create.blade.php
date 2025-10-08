@extends('backend.layouts.app')

@section('title')
    {{ __('Product Create') }} - {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div x-data="{ pageName: '{{ __('New Product') }}' }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('New Product') }}</h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                            {{ __('Home') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.products.index') }}">
                            {{ __('Products') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">
                        {{ __('New Product') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Create New Product') }}</h3>
            </div>
            <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                @include('backend.layouts.partials.messages')
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name_en" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Product Name (EN)') }}</label>
                            <input type="text" name="name_en" id="name_en" required value="{{ old('name_en') }}" placeholder="{{ __('Enter Product Name in English') }}" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="name_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Product Name (AR)') }}</label>
                            <input type="text" name="name_ar" id="name_ar" required value="{{ old('name_ar') }}" placeholder="{{ __('Enter Product Name in Arabic') }}" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('SKU') }}</label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" placeholder="{{ __('Enter SKU') }}" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Brand') }}</label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="{{ __('Enter Brand') }}" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Price') }}</label>
                            <input type="number" step="0.01" name="price" id="price" required value="{{ old('price') }}" placeholder="{{ __('Enter Price') }}" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Stock Quantity') }}</label>
                            <input type="number" name="stock" id="stock" required value="{{ old('stock') }}" placeholder="{{ __('Enter Stock Quantity') }}" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Product Image') }}</label>
                            <input type="file" name="image" id="image" accept="image/*" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-input  form-control">
                                <option value="1" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                                <option value="0" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="description_en" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Description (EN)') }}</label>
                            <textarea name="description_en" id="description_en" rows="3" class="form-input  form-control">{{ old('description_en') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="description_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Description (AR)') }}</label>
                            <textarea name="description_ar" id="description_ar" rows="3" class="form-input  form-control">{{ old('description_ar') }}</textarea>
                        </div>

                        {!! ld_apply_filters('after_product_fields', '', null) !!}
                    </div>

                    <div class="mt-6 flex justify-start gap-4">
                        <button type="submit" class="btn-primary">{{ __('Save') }}</button>
                        <a href="{{ route('admin.products.index') }}" class="btn-default">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
