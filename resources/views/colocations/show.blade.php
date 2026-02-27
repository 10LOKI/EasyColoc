<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $colocation->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Invite Code: {{ $colocation->invite_code }}</p>
            </div>
            <div class="flex gap-2">
                @if($colocation->isOwner(auth()->user()))
                <a href="{{ route('colocations.edit', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Edit
                </a>
                <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Cancel Colocation
                </button>
                @endif
                <a href="{{ route('expenses.create', ['colocation' => $colocation->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Expense
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <x-ui.stat-card 
                    title="Total Members" 
                    value="{{ $colocation->memberships->count() }}"
                    color="indigo"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z\'/></svg>'"
                />
                
                <x-ui.stat-card 
                    title="Total Expenses" 
                    value="${{ number_format($colocation->expenses->sum('amount'), 2) }}"
                    color="green"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
                />
                
                <x-ui.stat-card 
                    title="Your Share" 
                    value="${{ number_format($colocation->expenses->sum('amount') / max($colocation->memberships->count(), 1), 2) }}"
                    color="yellow"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z\'/></svg>'"
                />
            </div>

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
                                @if($membership->role === 'owner')
                                <x-ui.badge variant="primary">Owner</x-ui.badge>
                                @endif
                            </div>
                            @endif
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button class="w-full text-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                Invite Member
                            </button>
                        </div>
                    </x-ui.card>
                </div>

                <div class="lg:col-span-2">
                    <x-ui.card title="Recent Expenses">
                        @if($colocation->expenses->count() > 0)
                        <div class="space-y-3">
                            @foreach($colocation->expenses->take(10) as $expense)
                            <x-expense.card :expense="$expense" />
                            @endforeach
                        </div>
                        @else
                        <x-ui.empty-state 
                            title="No expenses yet"
                            description="Start tracking expenses for this colocation."
                            :action="route('expenses.create', ['colocation' => $colocation->id])"
                            actionText="Add First Expense"
                        />
                        @endif
                    </x-ui.card>
                </div>

            </div>

        </div>
    </div>

    {{-- Cancel Confirmation Modal --}}
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
</x-app-layout>
