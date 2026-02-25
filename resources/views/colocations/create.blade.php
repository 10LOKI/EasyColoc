<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create New Colocation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <x-ui.card>
                <form method="POST" action="{{ route('colocations.store') }}" class="space-y-6">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <x-input-label for="name" value="Colocation Name" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('name')" 
                            required 
                            autofocus 
                            placeholder="e.g., Downtown Apartment"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    {{-- Address --}}
                    <div>
                        <x-input-label for="address" value="Address" />
                        <x-text-input 
                            id="address" 
                            name="address" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('address')" 
                            required 
                            placeholder="123 Main St, City, Country"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    {{-- Description --}}
                    <div>
                        <x-input-label for="description" value="Description (Optional)" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Add any additional details about this colocation..."
                        >{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('colocations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-primary-button>
                            Create Colocation
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
