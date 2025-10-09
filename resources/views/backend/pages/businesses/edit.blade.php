@extends('backend.layouts.app')

@section('title')
    {{ __('Edit Business') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-4xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Edit Business') }}: {{ $business->name }}</h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.businesses.index') }}">
                        {{ __('Businesses') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>

    @include('backend.layouts.partials.messages')

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('admin.businesses.update', $business->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Same form fields as create.blade.php but with values from $business -->
            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Basic Information') }}</h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('Business Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $business->name) }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('Email') }}
                            </label>
                            <input type="email" name="email" id="email"
                                   value="{{ old('email', $business->email) }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('Phone') }}
                            </label>
                            <input type="text" name="phone" id="phone"
                                   value="{{ old('phone', $business->phone) }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('Address') }}
                            </label>
                            <textarea name="address" id="address" rows="3"
                                      class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">{{ old('address', $business->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Configuration -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('WhatsApp Configuration') }}</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="whatsapp_phone_number_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('WhatsApp Phone Number ID') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="whatsapp_phone_number_id" id="whatsapp_phone_number_id" required
                                   value="{{ old('whatsapp_phone_number_id', $business->whatsapp_phone_number_id) }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono">
                            <p class="text-xs text-gray-500 mt-1">{{ __('From WhatsApp Business API Dashboard') }}</p>
                            @error('whatsapp_phone_number_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="whatsapp_business_account_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('WhatsApp Business Account ID') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="whatsapp_business_account_id" id="whatsapp_business_account_id" required
                                   value="{{ old('whatsapp_business_account_id', $business->whatsapp_business_account_id) }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono">
                            @error('whatsapp_business_account_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="whatsapp_access_token" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('WhatsApp Access Token') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="whatsapp_access_token" id="whatsapp_access_token" required rows="3"
                                      class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono text-sm">{{ old('whatsapp_access_token', $business->whatsapp_access_token) }}</textarea>
                            @error('whatsapp_access_token')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="whatsapp_catalog_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('WhatsApp Catalog ID') }} <span class="text-gray-400">({{ __('Optional') }})</span>
                            </label>
                            <input type="text" name="whatsapp_catalog_id" id="whatsapp_catalog_id"
                                   value="{{ old('whatsapp_catalog_id', $business->whatsapp_catalog_id) }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono">
                            @error('whatsapp_catalog_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i class="bi bi-info-circle text-yellow-600 dark:text-yellow-400 text-xl"></i>
                                <div>
                                    <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-1">{{ __('Webhook Verify Token') }}</h4>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-2">
                                        {{ __('Use this token when setting up your WhatsApp webhook:') }}
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <code class="text-sm bg-yellow-100 dark:bg-yellow-900 px-3 py-1 rounded font-mono">
                                            {{ $business->whatsapp_verify_token }}
                                        </code>
                                        <button type="button" 
                                                onclick="navigator.clipboard.writeText('{{ $business->whatsapp_verify_token }}')"
                                                class="text-yellow-600 hover:text-yellow-700 dark:text-yellow-400">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Business Settings') }}</h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('Currency') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="currency" id="currency" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                                <option value="KWD" {{ old('currency', $business->currency) == 'KWD' ? 'selected' : '' }}>KWD - Kuwaiti Dinar</option>
                                <option value="USD" {{ old('currency', $business->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $business->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency', $business->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="AED" {{ old('currency', $business->currency) == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                <option value="SAR" {{ old('currency', $business->currency) == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                            </select>
                            @error('currency')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="delivery_charge" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                {{ __('Delivery Charge') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="delivery_charge" id="delivery_charge" required
                                   value="{{ old('delivery_charge', $business->delivery_charge) }}"
                                   step="0.001"
                                   min="0"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                            @error('delivery_charge')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $business->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400">
                                    {{ __('Business is Active') }}
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-6">
                                {{ __('Inactive businesses will not receive or process WhatsApp messages') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                    <i class="bi bi-check-circle mr-1"></i>
                    {{ __('Update Business') }}
                </button>
                <a href="{{ route('admin.businesses.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection