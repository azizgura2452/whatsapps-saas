@extends('backend.layouts.app')

@section('title')
    Broadcasts - {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Broadcasts</h1>
            <a href="{{ route('admin.broadcasts.create') }}" class="btn-primary">New Broadcast</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-xl">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Title</th>
                        <th class="p-3 text-left">Recipients</th>
                        <th class="p-3 text-left">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($broadcasts as $broadcast)
                        <tr class="border-t">
                            <td class="p-3">{{ $broadcast->id }}</td>
                            <td class="p-3">{{ $broadcast->whatsapp_template_name ?? '-' }}</td>
                            <td class="p-3">
                                @if ($broadcast->custom_recipients)
                                    <span class="cursor-pointer underline decoration-dotted text-blue-600"
                                        title="{{ $broadcast->custom_recipients }}">
                                        Custom
                                    </span>
                                @else
                                    All Customers
                                @endif
                            </td>

                            <td class="p-3">{{ $broadcast->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $broadcasts->links() }}</div>
    </div>
@endsection