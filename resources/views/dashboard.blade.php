<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('invitations.show') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    Join Colocation
                </a>
                <a href="{{ route('colocations.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    New Colocation
                </a>
            </div>
        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                <x-ui.stat-card 
                    title="Total Colocations" 
                    value="{{ $stats['colocations'] ?? 0 }}"
                    color="indigo"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6\'/></svg>'"
                />
                
                <x-ui.stat-card 
                    title="Total Expenses" 
                    value="${{ number_format($stats['total_expenses'] ?? 0, 2) }}"
                    color="green"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
                />
                
                <x-ui.stat-card 
                    title="Your Balance" 
                    value="${{ number_format($stats['balance'] ?? 0, 2) }}"
                    color="yellow"
                    :trend="5"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z\'/></svg>'"
                />
                
                <x-ui.stat-card 
                    title="Pending Settlements" 
                    value="{{ $stats['pending_settlements'] ?? 0 }}"
                    color="red"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
                />

                <x-ui.stat-card 
                    title="Reputation" 
                    value="{{ auth()->user()->reputation_score }}"
                    color="{{ auth()->user()->reputation_score < 0 ? 'red' : 'green' }}"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.076 3.317a1 1 0 00.95.69h3.49c.969 0 1.371 1.24.588 1.81l-2.824 2.052a1 1 0 00-.364 1.118l1.078 3.318c.3.922-.755 1.688-1.54 1.118l-2.825-2.052a1 1 0 00-1.175 0l-2.824 2.052c-.785.57-1.84-.196-1.54-1.118l1.077-3.318a1 1 0 00-.363-1.118L2.945 8.744c-.783-.57-.38-1.81.588-1.81h3.49a1 1 0 00.95-.69l1.076-3.317z\'/></svg>'"
                />
            </div>

            {{-- Recent Activity Alert --}}
            @if(session('success'))
            <x-ui.alert type="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
            @endif
<!-- nadee -->
            {{-- My Colocations --}}
            <x-ui.card title="My Colocations">
                @if(isset($colocations) && $colocations->count() > 0)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($colocations as $colocation)
                    <x-colocation.card :colocation="$colocation" />
                    @endforeach
                </div>
                @else
                <x-ui.empty-state 
                    title="No colocations yet"
                    description="Get started by creating your first colocation."
                    :action="route('colocations.create')"
                    actionText="Create Colocation"
                />
                @endif
            </x-ui.card>

            {{-- Recent Expenses --}}
            <x-ui.card title="Recent Expenses">
                @if(isset($recentExpenses) && $recentExpenses->count() > 0)
                <div class="space-y-3">
                    @foreach($recentExpenses as $expense)
                    <x-expense.card :expense="$expense" />
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('expenses.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        View all expenses →
                    </a>
                </div>
                @else
                <x-ui.empty-state 
                    title="No expenses yet"
                    description="Start tracking your shared expenses."
                    :action="route('expenses.create')"
                    actionText="Add Expense"
                />
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
