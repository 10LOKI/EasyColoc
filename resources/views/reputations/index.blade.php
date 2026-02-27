<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reputation
            </h2>
            <x-ui.badge :variant="$totalScore < 0 ? 'danger' : 'success'">
                Total Score: {{ $totalScore }}
            </x-ui.badge>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <x-ui.alert type="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
            @endif

            <x-ui.card title="Add Reputation Entry">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <form method="POST" action="{{ route('reputations.positive') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="score_type" value="positive">
                        <div>
                            <x-input-label for="positive_user_id" value="Target member" />
                            <select id="positive_user_id" name="user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select member</option>
                                @foreach($memberOptions as $option)
                                <option value="{{ $option['user_id'] }}" data-colocation-id="{{ $option['colocation_id'] }}">
                                    {{ $option['user_name'] }} ({{ $option['colocation_name'] }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>
                        <div>
                            <x-input-label for="positive_colocation_id" value="Colocation (optional)" />
                            <select id="positive_colocation_id" name="colocation_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">General</option>
                                @foreach($colocations as $colocation)
                                <option value="{{ $colocation->id }}">{{ $colocation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="positive_reason" value="Reason" />
                            <x-text-input id="positive_reason" name="reason" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="positive_description" value="Description (optional)" />
                            <textarea id="positive_description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <x-primary-button>
                            Add Positive (+1)
                        </x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('reputations.negative') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="score_type" value="negative">
                        <div>
                            <x-input-label for="negative_user_id" value="Target member" />
                            <select id="negative_user_id" name="user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select member</option>
                                @foreach($memberOptions as $option)
                                <option value="{{ $option['user_id'] }}" data-colocation-id="{{ $option['colocation_id'] }}">
                                    {{ $option['user_name'] }} ({{ $option['colocation_name'] }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>
                        <div>
                            <x-input-label for="negative_colocation_id" value="Colocation (optional)" />
                            <select id="negative_colocation_id" name="colocation_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">General</option>
                                @foreach($colocations as $colocation)
                                <option value="{{ $colocation->id }}">{{ $colocation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="negative_reason" value="Reason" />
                            <x-text-input id="negative_reason" name="reason" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="negative_description" value="Description (optional)" />
                            <textarea id="negative_description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <x-danger-button>
                            Add Negative (-1)
                        </x-danger-button>
                    </form>
                </div>
            </x-ui.card>

            <x-ui.card title="My Reputation History">
                @if($reputations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colocation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reputations as $entry)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-ui.badge :variant="$entry->score < 0 ? 'danger' : 'success'">
                                        {{ $entry->score > 0 ? '+' . $entry->score : $entry->score }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $entry->reason }}</p>
                                    @if($entry->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ $entry->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $entry->colocation?->name ?? 'General' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $entry->created_at?->format('M d, Y H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $reputations->links() }}
                </div>
                @else
                <x-ui.empty-state
                    title="No reputation entries yet"
                    description="Reputation events will appear here."
                />
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
