@props(['colocation'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ $colocation->name }}</h3>
                <p class="mt-1 text-sm text-gray-500">Code: {{ $colocation->invite_code }}</p>
            </div>
            <x-ui.badge variant="info">
                {{ $colocation->memberships_count ?? $colocation->memberships->count() }} members
            </x-ui.badge>
        </div>
        
        <div class="mt-4 flex items-center justify-between">
            <div class="flex -space-x-2">
                @foreach(($colocation->memberships ?? collect())->take(3) as $membership)
                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-gray-300 flex items-center justify-center text-xs font-medium text-gray-700">
                    {{ substr($membership->user->name, 0, 1) }}
                </div>
                @endforeach
                @if(($colocation->memberships_count ?? $colocation->memberships->count()) > 3)
                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600">
                    +{{ ($colocation->memberships_count ?? $colocation->memberships->count()) - 3 }}
                </div>
                @endif
            </div>
            
            <a href="{{ route('colocations.show', $colocation) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                View details →
            </a>
        </div>
    </div>
</div>
