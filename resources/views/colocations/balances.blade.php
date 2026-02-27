<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $colocation->name }} - Balances
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('colocations.show', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Colocation
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <x-ui.alert type="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
            @endif

            @if($errors->any())
            <x-ui.alert type="danger">
                {{ $errors->first() }}
            </x-ui.alert>
            @endif

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-ui.stat-card 
                    title="Total Expenses" 
                    value="${{ number_format($totalExpenses, 2) }}"
                    color="green"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
                />
                
                <x-ui.stat-card 
                    title="Per Person Share" 
                    value="${{ number_format($perPerson, 2) }}"
                    color="blue"
                    :icon="'<svg class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'/></svg>'"
                />
            </div>

            {{-- Balances --}}
            <x-ui.card title="Member Balances">
                <div class="space-y-4">
                    @foreach($balances as $balance)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-lg font-medium text-indigo-700">
                                {{ substr($balance['name'], 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <p class="text-lg font-medium text-gray-900">{{ $balance['name'] }}</p>
                                <p class="text-sm text-gray-500">Paid: ${{ number_format($balance['paid'], 2) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($balance['owes'] > 0)
                                <p class="text-lg font-semibold text-red-600">Owes ${{ number_format($balance['owes'], 2) }}</p>
                            @elseif($balance['owes'] < 0)
                                <p class="text-lg font-semibold text-green-600">Is owed ${{ number_format(abs($balance['owes']), 2) }}</p>
                            @else
                                <p class="text-lg font-semibold text-gray-600">Settled up</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card title="Suggested Settlements">
                @if(count($suggestedSettlements) > 0)
                <div class="space-y-3">
                    @foreach($suggestedSettlements as $settlement)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">{{ $settlement['from_name'] }}</span>
                                should pay
                                <span class="font-semibold">{{ $settlement['to_name'] }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Suggested amount: ${{ number_format($settlement['amount'], 2) }}
                            </p>
                        </div>
                        @if(auth()->id() === $settlement['from_id'])
                        <form method="POST" action="{{ route('colocations.settlements.store', $colocation) }}">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $settlement['to_id'] }}">
                            <input type="hidden" name="amount" value="{{ number_format($settlement['amount'], 2, '.', '') }}">
                            <x-primary-button type="submit">
                                Create Payment
                            </x-primary-button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No settlement needed. Everyone is balanced.</p>
                @endif
            </x-ui.card>

            <x-ui.card title="Pending Settlements">
                @if($pendingSettlements->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingSettlements as $pending)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">{{ $pending->sender?->name }}</span>
                                pays
                                <span class="font-semibold">{{ $pending->receiver?->name }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                ${{ number_format($pending->amount, 2) }} • {{ $pending->created_at?->format('M d, Y H:i') }}
                            </p>
                        </div>
                        @if(auth()->id() === $pending->sender_id)
                        <form method="POST" action="{{ route('colocations.settlements.pay', [$colocation, $pending]) }}">
                            @csrf
                            @method('PATCH')
                            <x-primary-button type="submit">
                                Mark As Paid
                            </x-primary-button>
                        </form>
                        @else
                        <x-ui.badge variant="warning">Pending</x-ui.badge>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No pending settlements.</p>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
