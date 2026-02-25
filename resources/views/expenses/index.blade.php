<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                All Expenses
            </h2>
            <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Expense
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <x-ui.card>
                <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <x-input-label for="colocation" value="Colocation" />
                        <select id="colocation" name="colocation" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Colocations</option>
                            @foreach($colocations ?? [] as $colocation)
                            <option value="{{ $colocation->id }}" {{ request('colocation') == $colocation->id ? 'selected' : '' }}>
                                {{ $colocation->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="category" value="Category" />
                        <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="date_from" value="From Date" />
                        <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="request('date_from')" />
                    </div>

                    <div class="flex items-end">
                        <x-primary-button class="w-full justify-center">
                            Filter
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Expenses List --}}
            <x-ui.card title="Expenses">
                @if($expenses->count() > 0)
                <div class="space-y-3">
                    @foreach($expenses as $expense)
                    <x-expense.card :expense="$expense" />
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $expenses->links() }}
                </div>
                @else
                <x-ui.empty-state 
                    title="No expenses found"
                    description="Start adding expenses to track your shared costs."
                    :action="route('expenses.create')"
                    actionText="Add Your First Expense"
                />
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
