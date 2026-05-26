@extends('layouts.app')
@section('title', 'Grāmatas')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Grāmatas</h1>
        <a href="{{ route('books.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Pievienot grāmatu</a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Nosaukums</th>
                    <th class="px-4 py-2 text-left">ISBN</th>
                    <th class="px-4 py-2 text-left">Pieejamie eksemplāri</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $book->title }}</td>
                        <td class="px-4 py-2">{{ $book->isbn }}</td>
                        <td class="px-4 py-2">{{ $book->available_copies }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('books.show', $book) }}" class="text-blue-600 hover:underline">Skatīt</a>
                            <a href="{{ route('books.edit', $book) }}" class="text-yellow-600 hover:underline">Labot</a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Vai dzēst?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">Nav grāmatu</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $books->links() }}</div>
@endsection
