<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliotēka - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow mb-6">
        <div class="max-w-7xl mx-auto px-4 py-3 flex gap-6">
            <a href="{{ route('books.index') }}" class="text-blue-600 hover:underline">Grāmatas</a>
            <a href="{{ route('readers.index') }}" class="text-blue-600 hover:underline">Lasītāji</a>
            <a href="{{ route('borrowings.index') }}" class="text-blue-600 hover:underline">Aizņēmumi</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
