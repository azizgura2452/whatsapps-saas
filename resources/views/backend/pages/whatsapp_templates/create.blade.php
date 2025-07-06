@extends('backend.layouts.app')

@section('title')
    {{ __('New WhatsApp Template') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('New WhatsApp Template') }}' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('New WhatsApp Template') }}</h2>
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
                                href="{{ route('admin.whatsapp-templates.index') }}">
                                {{ __('WhatsApp Templates') }}
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">
                            {{ __('New Template') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ __('Create New WhatsApp Template') }}
                    </h3>
                </div>
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    @include('backend.layouts.partials.messages')
                    <form action="{{ route('admin.whatsapp-templates.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Template Title') }}</label>
                                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                    placeholder="{{ __('Enter Template Title') }}" class="form-input form-control">
                            </div>

                            <div>
                                <label for="is_active"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Status') }}</label>
                                <select name="is_active" id="is_active" class="form-input form-control">
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>{{ __('Active') }}
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>{{ __('Inactive') }}
                                    </option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="message"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Message Body') }}</label>
                                <textarea name="message" id="message" rows="6" class="form-input form-control"
                                    placeholder="{{ __('Enter Template Message') }}">{{ old('message') }}</textarea>
                            </div>

                            {!! ld_apply_filters('after_whatsapp_template_fields', '', null) !!}
                        </div>

                        <div class="mt-6 flex justify-start gap-4">
                            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('admin.whatsapp-templates.index') }}"
                                class="btn-default">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection