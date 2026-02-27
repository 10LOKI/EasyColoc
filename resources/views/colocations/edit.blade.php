<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Colocation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <x-ui.card>
                <form id="update-colocation-form" method="POST" action="{{ route('colocations.update', $colocation) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Colocation Name" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('name', $colocation->name)" 
                            required 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="flex items-center justify-end">
                        <div class="flex gap-4">
                            <a href="{{ route('colocations.show', $colocation) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-primary-button>
                                Update Colocation
                            </x-primary-button>
                        </div>
                    </div>
                </form>

                <div class="mt-6 border-t border-gray-200 pt-6">
                    <form method="POST" action="{{ route('colocations.destroy', $colocation) }}">
                        @csrf
                        @method('DELETE')
                        <x-danger-button
                            type="submit"
                            onclick="return confirm('Are you sure you want to cancel this colocation?')"
                        >
                            Cancel Colocation
                        </x-danger-button>
                    </form>
                </div>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
