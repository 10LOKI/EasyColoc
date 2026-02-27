@props(['colocation'])

@php
    $activeMemberships = ($colocation->memberships ?? collect())->whereNull('left_at');
    $activeMembersCount = $colocation->memberships_count ?? $activeMemberships->count();
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ $colocation->name }}</h3>
                <p class="mt-1 text-sm text-gray-500">Code: {{ $colocation->invite_code }}</p>
                <div class="mt-2">
                    <x-ui.badge :variant="$colocation->status === 'cancelled' ? 'danger' : 'success'">
                        {{ ucfirst($colocation->status ?? 'active') }}
                    </x-ui.badge>
                </div>
            </div>
            <x-ui.badge variant="info">
                {{ $activeMembersCount }} members
            </x-ui.badge>
        </div>
        
        <div class="mt-4 flex items-center justify-between">
            <div class="flex -space-x-2">
                @foreach($activeMemberships->take(3) as $membership)
                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-gray-300 flex items-center justify-center text-xs font-medium text-gray-700">
                    {{ substr($membership->user->name, 0, 1) }}
                </div>
                @endforeach
                @if($activeMembersCount > 3)
                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600">
                    +{{ $activeMembersCount - 3 }}
                </div>
                @endif
            </div>
            
            <a href="{{ route('colocations.show', $colocation) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                View details →
            </a>
        </div>
    </div>
</div>
