<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $colocations->name }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('colocations.balances', $colocations) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    View Balances
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Add Expense Form --}}
            <x-ui.card title="Add New Expense">
                <form action="{{ route('colocations.expenses', $colocations) }}" method="POST">
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

            {{-- Members & Expenses --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-1">
                    <x-ui.card title="Members">
                        <div class="space-y-3">
                            @foreach($colocations->users as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-medium text-indigo-700">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->pivot->role }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button onclick="document.getElementById('inviteModal').classList.remove('hidden')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                </svg>
                                Invite Member
                            </button>
                        </div>
                    </x-ui.card>
                </div>

                <div class="lg:col-span-2">
                    <x-ui.card title="Recent Expenses">
                        @if($colocations->expenses->count() > 0)
                        <div class="space-y-3">
                            @foreach($colocations->expenses as $expense)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $expense->description }}</p>
                                    <p class="text-sm text-gray-500">Paid by {{ $expense->paidBy->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">${{ number_format($expense->amount, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $expense->created_at->format('M d, Y') }}</p>
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

    {{-- Invite Modal --}}
    <div id="inviteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Invite Link</h3>
                <button onclick="document.getElementById('inviteModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Share this link to invite members:</label>
                <div class="flex gap-2">
                    <input type="text" id="inviteLink" readonly value="{{ url('/join?code=' . $colocations->invite_code) }}" class="flex-1 rounded-md border-gray-300 bg-gray-50 shadow-sm">
                    <button onclick="copyInviteLink()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Copy
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyInviteLink() {
        const input = document.getElementById('inviteLink');
        input.select();
        document.execCommand('copy');
        alert('Invite link copied to clipboard!');
    }
    </script>
</x-app-layout>
