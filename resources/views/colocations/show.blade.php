<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $colocation->name }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ $colocation->address }}</p>
        </div>
        <div class="flex gap-2">
            @if($colocation->isOwner(auth()->user()))
            <a href="{{ route('colocations.edit', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Edit
            </a>
            @endif
            <a href="{{ route('expenses.create', ['colocation' => $colocation->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add Expense
            </a>
        </div>
    </div>
</x-slot>
