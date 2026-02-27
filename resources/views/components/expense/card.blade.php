@props(['expense'])

<div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h4 class="text-sm font-medium text-gray-900">{{ $expense->description }}</h4>
                <x-ui.badge variant="default">{{ $expense->category->name ?? 'Uncategorized' }}</x-ui.badge>
            </div>
            <p class="mt-1 text-xs text-gray-500">
                Paid by {{ $expense->payer?->name ?? 'Unknown user' }} • {{ $expense->date?->format('M d, Y') ?? $expense->created_at?->format('M d, Y') ?? 'No date' }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-lg font-semibold text-gray-900">${{ number_format($expense->amount, 2) }}</p>
        </div>
    </div>
</div>
