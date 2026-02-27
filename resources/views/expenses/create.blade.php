<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add New Expense
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <x-ui.card>
                <form method="POST" action="{{ route('expenses.store') }}" class="space-y-6">
                    @csrf

                    {{-- Colocation --}}
                    <div>
                        <x-input-label for="colocation_id" value="Colocation" />
                        <select 
                            id="colocation_id" 
                            name="colocation_id" 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="">Select a colocation</option>
                            @foreach($colocations ?? [] as $colocation)
                            <option value="{{ $colocation->id }}" {{ old('colocation_id', request('colocation')) == $colocation->id ? 'selected' : '' }}>
                                {{ $colocation->name }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('colocation_id')" />
                    </div>

                    {{-- Description --}}
                    <div>
                        <x-input-label for="description" value="Description" />
                        <x-text-input 
                            id="description" 
                            name="description" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('description')" 
                            required 
                            placeholder="e.g., Groceries, Electricity bill"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    {{-- Amount --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="amount" value="Amount" />
                            <x-text-input 
                                id="amount" 
                                name="amount" 
                                type="number" 
                                step="0.01"
                                class="mt-1 block w-full" 
                                :value="old('amount')" 
                                required 
                                placeholder="0.00"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('expenses.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-primary-button>
                            Add Expense
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
