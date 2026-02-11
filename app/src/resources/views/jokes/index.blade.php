<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programming Jokes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background-color: #f5f5f5;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .jokes-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .joke {
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #4a90d9;
            background-color: #f8f9fa;
            border-radius: 0 4px 4px 0;
        }

        .joke:last-child {
            margin-bottom: 0;
        }

        .joke-type {
            font-size: 0.85em;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .joke-text {
            color: #333;
            font-size: 1.1em;
        }

        .error-message {
            padding: 15px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .empty-message {
            padding: 15px;
            background-color: #fff3cd;
            color: #856404;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .refresh-button {
            display: block;
            width: 100%;
            padding: 12px 24px;
            background-color: #4a90d9;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .refresh-button:hover {
            background-color: #357abd;
        }

        .refresh-button:active {
            background-color: #2a6299;
        }
    </style>
</head>
<body>
    <h1>Programming Jokes</h1>

    @if ($error)
        <div class="error-message">
            {{ $error }}
        </div>
    @endif

    <div class="jokes-container">
        @if (empty($jokes) && !$error)
            <div class="empty-message">
                No jokes available at the moment. Please try refreshing.
            </div>
        @else
            @foreach ($jokes as $joke)
                <div class="joke">
                    <div class="joke-type">{{ $joke['type'] ?? 'Programming' }}</div>
                    <div class="joke-text">{{ $joke['setup'] ?? $joke['joke'] ?? 'No joke text available' }}</div>
                    @if (isset($joke['punchline']))
                        <div class="joke-text" style="margin-top: 10px; font-style: italic;">
                            {{ $joke['punchline'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <form method="GET" action="{{ route('jokes.index') }}">
        <button type="submit" class="refresh-button">Refresh Jokes</button>
    </form>
</body>
</html>
