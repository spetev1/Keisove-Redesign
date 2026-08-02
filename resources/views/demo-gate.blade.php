<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name') }}</title>
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            background: #0a0a0a;
            color: #fafafa;
            font: 400 15px/1.5 ui-sans-serif, system-ui, -apple-system, sans-serif;
        }
        .card {
            width: 100%;
            max-width: 22rem;
            padding: 2rem;
            border: 1px solid #262626;
            border-radius: 0.875rem;
            background: #131313;
        }
        h1 { margin: 0 0 0.375rem; font-size: 1.125rem; letter-spacing: -0.01em; }
        p { margin: 0 0 1.5rem; color: #a1a1aa; font-size: 0.875rem; }
        label { display: block; margin-bottom: 0.5rem; font-size: 0.8125rem; color: #d4d4d8; }
        input {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid #333;
            border-radius: 0.5rem;
            background: #0a0a0a;
            color: #fafafa;
            font: inherit;
        }
        input:focus { outline: 2px solid #525252; outline-offset: 1px; border-color: transparent; }
        button {
            width: 100%;
            margin-top: 1rem;
            padding: 0.625rem 0.75rem;
            border: 0;
            border-radius: 0.5rem;
            background: #fafafa;
            color: #0a0a0a;
            font: 500 inherit;
            cursor: pointer;
        }
        button:hover { background: #e4e4e7; }
        .error { margin: 0.75rem 0 0; color: #f87171; font-size: 0.8125rem; }
    </style>
</head>
<body>
    <main class="card">
        <h1>{{ config('app.name') }}</h1>
        <p>This preview is not public yet. Enter the password you were given to continue.</p>

        <form method="POST" action="{{ route('demo.unlock') }}">
            @csrf
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autofocus autocomplete="current-password" required>
            <button type="submit">View the preview</button>
            @if ($failed)
                <p class="error">That password is not right.</p>
            @endif
        </form>
    </main>
</body>
</html>
