@extends('backend.layouts.app')

@section('title', __('Broadcast Groups') . ' | ' . config('app.name'))

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Broadcast Groups') }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                       href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Broadcast Groups') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ __('Groups List') }}
                </h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.broadcast-groups.create') }}" class="btn-primary">
                        <i class="bi bi-plus-circle mr-2"></i>
                        {{ __('New Group') }}
                    </a>
                </div>
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                @include('backend.layouts.partials.messages')

                <table class="w-full dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5" width="5%">
                                #
                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                {{ __('Name') }}
                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                {{ __('Description') }}
                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                {{ __('Customers') }}
                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                {{ __('Conditions') }}
                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5" width="15%">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6">{{ $groups->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $group->name }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $group->description ?? '-' }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <i class="bi bi-people mr-1"></i>
                                        {{ $group->getCustomerCount() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    @forelse($group->conditions as $cond)
                                        <span class="inline-block text-xs bg-gray-100 dark:bg-gray-700 rounded px-2 py-1 mr-1 mb-1">
                                            {{ $cond->field }} {{ $cond->operator }} {{ $cond->value }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">{{ __('No conditions') }}</span>
                                    @endforelse
                                </td>
                                <td class="px-5 py-4 sm:px-6 flex gap-2">
                                    <a href="{{ route('admin.broadcast-groups.edit', $group->id) }}" 
                                       class="btn-default !p-2"
                                       title="{{ __('Edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('admin.broadcast-groups.destroy', $group->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this group?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger !p-2"
                                                title="{{ __('Delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('No groups found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    {{ $groups->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection