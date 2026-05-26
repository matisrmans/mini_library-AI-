@extends('layouts.app')
@section('title', 'Aizņēmumi')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Aizņēmumi</h1>
        <a href="{{ route('borrowings.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Pievienot aizņēmumu</a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Grāmata</th>
                    <th class="px-4 py-2 text-left">Lasītājs</th>
                    <th class="px-4 py-2 text-left">Aizņemšanas datums</th>
                    <th class="px-4 py-2 text-left">Atdošanas datums</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($borrowings as $borrowing)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $borrowing->book->title }}</td>
                        <td class="px-4 py-2">{{ $borrowing->reader->name }}</td>
                        <td class="px-4 py-2">{{ $borrowing->borrowed_at }}</td>
                        <td class="px-4 py-2">{{ $borrowing->returned_at ?? '—' }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('borrowings.show', $borrowing) }}" class="text-blue-600 hover:underline">Skatīt</a>
                            <a href="{{ route('borrowings.edit', $borrowing) }}" class="text-yellow-600 hover:underline">Labot</a>
                            <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" onsubmit="return confirm('Vai dzēst?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Nav aizņēmumu</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $borrowings->links() }}</div>
@endsection
