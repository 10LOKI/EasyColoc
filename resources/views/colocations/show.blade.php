<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $colocation->name }}
                </h2>
                <div class="mt-1">
                    <x-ui.badge :variant="$colocation->status === 'cancelled' ? 'danger' : 'success'">
                        {{ ucfirst($colocation->status ?? 'active') }}
                    </x-ui.badge>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('colocations.balances', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                    Settlements
                </a>
                @if($colocation->status !== 'cancelled' && $colocation->isOwner(auth()->user()))
                <a href="{{ route('colocations.edit', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Edit
                </a>
                <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Cancel Colocation
                </button>
                @endif
                @if($colocation->status !== 'cancelled')
                <a href="{{ route('expenses.create', ['colocation' => $colocation->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Expense
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if($colocation->status === 'cancelled')
            <x-ui.alert type="warning">
                This colocation is cancelled.
            </x-ui.alert>
            @endif

            {{-- Add Expense Form --}}
            @if($colocation->status !== 'cancelled')
            <x-ui.card title="Add New Expense">
                <form action="{{ route('colocations.expenses', $colocation) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount</label>
                            <input type="number" name="amount" step="0.01" min="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Add Expense
                            </button>
                        </div>
                    </div>
                </form>
            </x-ui.card>
            @endif

            {{-- Members & Expenses --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-1">
                    <x-ui.card title="Members">
                        <div class="space-y-3">
                            @foreach($colocation->memberships as $membership)
                            @if($membership->user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-medium text-indigo-700">
                                        {{ substr($membership->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $membership->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($membership->role) }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @if($colocation->status !== 'cancelled')
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button onclick="document.getElementById('inviteModal').classList.remove('hidden')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                </svg>
                                Invite Member
                            </button>
                        </div>
                        @endif
                    </x-ui.card>
                </div>

                <div class="lg:col-span-2">
                    <x-ui.card title="Recent Expenses">
                        @if($colocation->expenses->count() > 0)
                        <div class="space-y-3">
                            @foreach($colocation->expenses as $expense)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $expense->description }}</p>
                                    <p class="text-sm text-gray-500">Paid by {{ $expense->payer?->name ?? 'Unknown user' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">${{ number_format($expense->amount, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $expense->date?->format('M d, Y') ?? $expense->created_at?->format('M d, Y') ?? 'No date' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <x-ui.empty-state 
                            title="No expenses yet"
                            description="Start tracking expenses for this colocation."
                        />
                        @endif
                    </x-ui.card>
                </div>

            </div>

        </div>
    </div>

    {{-- Invite Member Modal --}}
    @if($colocation->status !== 'cancelled')
    <div id="inviteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-2">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Invite Member</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Share this code or link so someone can join your colocation.
                </p>

                <div class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Invite Code</label>
                        <input
                            id="inviteCodeInput"
                            type="text"
                            readonly
                            value="{{ $colocation->invite_code }}"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-900"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Invite Link</label>
                        <input
                            id="inviteLinkInput"
                            type="text"
                            readonly
                            value="{{ route('invitations.show', ['code' => $colocation->invite_code]) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-900"
                        >
                    </div>
                </div>

                <div class="mt-5 flex gap-2">
                    <button
                        type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('inviteLinkInput').value)"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Copy Link
                    </button>
                    <button
                        type="button"
                        onclick="document.getElementById('inviteModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Cancel Confirmation Modal --}}
    @if($colocation->status !== 'cancelled' && $colocation->isOwner(auth()->user()))
    <div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Cancel Colocation</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to cancel this colocation? This action will mark it as cancelled and all members will be removed.
                    </p>
                </div>
                <div class="flex gap-2 px-4 py-3">
                    <button onclick="document.getElementById('cancelModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        No, Keep It
                    </button>
                    <form action="{{ route('colocations.destroy', $colocation) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Yes, Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
