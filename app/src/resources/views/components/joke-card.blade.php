@props([
    'type' => 'programming',
    'joke' => null,
    'setup' => null,
    'punchline' => null,
])

<div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-blue-500">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 uppercase tracking-wide">
                {{ $type ?? 'Programming' }}
            </span>
        </div>

        @if($setup && $punchline)
            <div class="space-y-3">
                <p class="text-gray-900 text-lg font-medium">{{ $setup }}</p>
                <p class="text-gray-600 text-lg italic">{{ $punchline }}</p>
            </div>
        @elseif($joke)
            <p class="text-gray-900 text-lg">{{ $joke }}</p>
        @else
            <p class="text-gray-500 italic">No joke text available</p>
        @endif
    </div>
</div>
