<x-layout>
    <x-slot:title>Programming Jokes</x-slot>

    <div id="error-container">
        @if ($error)
            <x-alert type="error" class="mb-6">
                {{ $error }}
            </x-alert>
        @endif
    </div>

    <div id="loading-indicator" class="hidden mb-6">
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-3 text-gray-600">Loading jokes...</span>
        </div>
    </div>

    <div id="jokes-container" class="space-y-6 mb-6">
        @if (empty($jokes) && !$error)
            <x-alert type="warning">
                No jokes available at the moment. Please try refreshing.
            </x-alert>
        @else
            @foreach ($jokes as $joke)
                <x-joke-card
                    :type="$joke['type'] ?? 'programming'"
                    :joke="$joke['joke'] ?? null"
                    :setup="$joke['setup'] ?? null"
                    :punchline="$joke['punchline'] ?? null"
                />
            @endforeach
        @endif
    </div>

    <x-bladewind::button
        id="refresh-btn"
        color="blue"
        class="w-full"
        onclick="refreshJokes()"
    >
        Refresh Jokes
    </x-bladewind::button>

    <script>
        function refreshJokes() {
            const btn = document.getElementById('refresh-btn');
            const loading = document.getElementById('loading-indicator');
            const container = document.getElementById('jokes-container');
            const errorContainer = document.getElementById('error-container');

            btn.disabled = true;
            loading.classList.remove('hidden');
            errorContainer.innerHTML = '';

            fetch('/api/jokes', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch jokes');
                }
                return response.json();
            })
            .then(result => {
                const jokes = result.data || [];
                if (jokes.length === 0) {
                    container.innerHTML = `
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        No jokes available at the moment. Please try refreshing.
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = jokes.map(joke => renderJokeCard(joke)).join('');
            })
            .catch(error => {
                errorContainer.innerHTML = `
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Failed to load jokes. Please try again.
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            })
            .finally(() => {
                btn.disabled = false;
                loading.classList.add('hidden');
            });
        }

        function renderJokeCard(joke) {
            const type = joke.type || 'programming';
            const jokeText = joke.joke
                ? `<p class="text-gray-900 text-lg">${escapeHtml(joke.joke)}</p>`
                : `<div class="space-y-3">
                    <p class="text-gray-900 text-lg font-medium">${escapeHtml(joke.setup || '')}</p>
                    <p class="text-gray-600 text-lg italic">${escapeHtml(joke.punchline || '')}</p>
                   </div>`;

            return `
                <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-blue-500">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 uppercase tracking-wide">
                                ${escapeHtml(type)}
                            </span>
                        </div>
                        ${jokeText}
                    </div>
                </div>
            `;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</x-layout>
