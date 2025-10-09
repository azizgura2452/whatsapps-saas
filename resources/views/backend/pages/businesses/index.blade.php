@extends('backend.layouts.app')

@section('title')
    {{ __('Businesses') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-7xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('My Businesses') }}</h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Businesses') }}</li>
            </ol>
        </nav>
    </div>

    @include('backend.layouts.partials.messages')

    <!-- Current Business Card -->
    @if(isset($currentBusiness))
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center">
                    <i class="bi bi-building text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ __('Current Business') }}</p>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ $currentBusiness->name }}</h3>
                </div>
            </div>
            <span class="px-3 py-1 bg-blue-600 text-white text-sm rounded-full">
                <i class="bi bi-check-circle mr-1"></i>{{ __('Active') }}
            </span>
        </div>
    </div>
    @endif

    <!-- Businesses List -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('All Businesses') }}</h3>
                
                @if(auth()->user()->can('business.create'))
                <a href="{{ route('admin.businesses.create') }}" class="btn-primary">
                    <i class="bi bi-plus-circle mr-2"></i>
                    {{ __('Add Business') }}
                </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Business Name') }}</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('WhatsApp Phone ID') }}</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Currency') }}</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($businesses as $business)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $business->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $business->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                    {{ Str::limit($business->whatsapp_phone_number_id, 20) }}
                                </code>
                            </td>
                            <td class="p-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $business->currency }}</span>
                            </td>
                            <td class="p-4">
                                @if($business->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="bi bi-check-circle mr-1"></i>{{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        <i class="bi bi-pause-circle mr-1"></i>{{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    @if(session('current_business_id') != $business->id)
                                    <form action="{{ route('admin.businesses.switch', $business->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-success !p-2" title="{{ __('Switch to this business') }}">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if(auth()->user()->can('business.edit'))
                                    <a href="{{ route('admin.businesses.edit', $business->id) }}" class="btn-default !p-2" title="{{ __('Edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif

                                    @if(auth()->user()->can('business.delete') && $businesses->count() > 1)
                                    <button 
                                        data-modal-target="delete-modal-{{ $business->id }}" 
                                        data-modal-toggle="delete-modal-{{ $business->id }}"
                                        class="btn-danger !p-2" 
                                        title="{{ __('Delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- Delete Modal -->
                                    <div id="delete-modal-{{ $business->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                                        <div class="relative p-4 w-full max-w-md bg-white rounded-lg shadow-lg dark:bg-gray-700 z-60">
                                            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal-{{ $business->id }}">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                            </button>
                                            <div class="p-4 md:p-5 text-center">
                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('Are you sure you want to delete this business?') }}</h3>
                                                <form action="{{ route('admin.businesses.destroy', $business->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                        {{ __('Yes, Delete') }}
                                                    </button>
                                                    <button data-modal-hide="delete-modal-{{ $business->id }}" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                                        {{ __('Cancel') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-building text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">{{ __('No businesses found') }}</p>
                                    @if(auth()->user()->can('business.create'))
                                    <a href="{{ route('admin.businesses.create') }}" class="btn-primary">
                                        <i class="bi bi-plus-circle mr-2"></i>
                                        {{ __('Create Your First Business') }}
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($businesses->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
                {{ $businesses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection