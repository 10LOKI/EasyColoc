<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Colocations
            </h2>
            <a href="{{ route('colocations.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                New Colocation
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <x-ui.alert type="success" dismissible class="mb-6">
                {{ session('success') }}
            </x-ui.alert>
            @endif

            @if($colocations->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($colocations as $colocation)
                <x-colocation.card :colocation="$colocation" />
                @endforeach
            </div>
            @else
            <x-ui.card>
                <x-ui.empty-state 
                    title="No colocations yet"
                    description="Create your first colocation to start managing shared expenses with your roommates."
                    :action="route('colocations.create')"
                    actionText="Create Your First Colocation"
                />
            </x-ui.card>
            @endif

        </div>
    </div>
</x-app-layout>
