<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $colocation->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $colocation->address }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('expenses.create', ['colocation' => $colocation->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Expense
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
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
                
                {{-- Members --}}
                <div class="lg:col-span-1">
                    <x-ui.card title="Members">
                        <div class="space-y-3">
                            @foreach($colocation->memberships as $membership)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-medium text-indigo-700">
                                        {{ substr($membership->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $membership->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $membership->role }}</p>
                                    </div>
                                </div>
                                @if($membership->role === 'admin')
                                <x-ui.badge variant="primary">Admin</x-ui.badge>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button class="w-full text-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                Invite Member
                            </button>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Expenses --}}
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
</x-app-layout>
