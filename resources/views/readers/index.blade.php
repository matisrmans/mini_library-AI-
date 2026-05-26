@extends('layouts.app')
@section('title', 'Lasītāji')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Lasītāji</h1>
        <a href="{{ route('readers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Pievienot lasītāju</a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Vārds</th>
                    <th class="px-4 py-2 text-left">E-pasts</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($readers as $reader)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $reader->name }}</td>
                        <td class="px-4 py-2">{{ $reader->email }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('readers.show', $reader) }}" class="text-blue-600 hover:underline">Skatīt</a>
                            <a href="{{ route('readers.edit', $reader) }}" class="text-yellow-600 hover:underline">Labot</a>
                            <form action="{{ route('readers.destroy', $reader) }}" method="POST" onsubmit="return confirm('Vai dzēst?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Nav lasītāju</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $readers->links() }}</div>
@endsection
