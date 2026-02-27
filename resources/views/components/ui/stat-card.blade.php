@props(['title', 'value', 'icon' => null, 'trend' => null, 'color' => 'indigo'])

@php
$colors = [
    'indigo' => 'bg-indigo-500',
    'green' => 'bg-green-500',
    'red' => 'bg-red-500',
    'yellow' => 'bg-yellow-500',
    'blue' => 'bg-blue-500',
];
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-center">
            @if($icon)
            <div class="flex-shrink-0">
                <div class="{{ $colors[$color] }} rounded-md p-3">
                    {!! $icon !!}
                </div>
            </div>
            @endif
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">{{ $title }}</dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">{{ $value }}</div>
                        @if($trend)
                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $trend > 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($trend > 0)
                            <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                            <span class="ml-1">{{ abs($trend) }}%</span>
                        </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
