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
                                        onclick="showRecipients({{ json_encode(explode(',', $broadcast->custom_recipients)) }})">
                                        View Recepients
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
    <!-- Recipients Modal -->
    <div id="recipientsModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-[95%] max-w-md rounded-xl shadow-xl p-6 relative">
            <button onclick="closeRecipients()"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-600 text-2xl">&times;</button>

            <h2 class="text-lg font-semibold mb-4">All Recipients</h2>
            <div class="overflow-x-auto max-h-[400px] overflow-y-auto border rounded">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left border-b">#</th>
                            <th class="p-2 text-left border-b">Phone Number</th>
                        </tr>
                    </thead>
                    <tbody id="recipientsTableBody">
                        <!-- Filled dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script>
        function showRecipients(numbers) {
            const tableBody = document.getElementById('recipientsTableBody');
            tableBody.innerHTML = '';

            numbers.forEach((num, index) => {
                const row = `<tr class="border-t">
                                    <td class="p-2">${index + 1}</td>
                                    <td class="p-2">${num}</td>
                                </tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });

            document.getElementById('recipientsModal').classList.remove('hidden');
        }

        function closeRecipients() {
            document.getElementById('recipientsModal').classList.add('hidden');
        }
    </script>

@endsection