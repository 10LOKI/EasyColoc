<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Join a Colocation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            
            <x-ui.card>
                @if(session('error'))
                <x-ui.alert type="error" class="mb-6">
                    {{ session('error') }}
                </x-ui.alert>
                @endif

                @if(session('info'))
                <x-ui.alert type="info" class="mb-6">
                    {{ session('info') }}
                </x-ui.alert>
                @endif

                <form method="POST" action="{{ route('invitations.join') }}" class="space-y-6">
                    @csrf

                    <div class="text-center mb-6">
                        <svg class="mx-auto h-12 w-12 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Enter Invitation Code</h3>
                        <p class="mt-1 text-sm text-gray-500">Ask your roommate for the 8-character code</p>
                    </div>

                    <div>
                        <x-input-label for="invite_code" value="Invitation Code" />
                        <x-text-input 
                            id="invite_code" 
                            name="invite_code" 
                            type="text" 
                            class="mt-1 block w-full text-center text-2xl tracking-widest uppercase font-mono" 
                            :value="old('invite_code', request('code'))" 
                            required 
                            autofocus 
                            maxlength="8"
                            placeholder="ABC12345"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('invite_code')" />
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <x-primary-button>
                            Join Colocation
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
