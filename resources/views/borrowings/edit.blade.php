@extends('layouts.app')
@section('title', 'Labot aizņēmumu')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Labot aizņēmumu</h1>

    <form action="{{ route('borrowings.update', $borrowing) }}" method="POST" class="bg-white rounded shadow p-6 max-w-lg">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Grāmata</label>
            <select name="book_id" class="w-full border rounded px-3 py-2" required>
                @foreach ($books as $book)
                    <option value="{{ $book->id }}" {{ old('book_id', $borrowing->book_id) == $book->id ? 'selected' : '' }}>
                        {{ $book->title }} ({{ $book->available_copies }} pieejami)
                    </option>
                @endforeach
            </select>
            @error('book_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Lasītājs</label>
            <select name="reader_id" class="w-full border rounded px-3 py-2" required>
                @foreach ($readers as $reader)
                    <option value="{{ $reader->id }}" {{ old('reader_id', $borrowing->reader_id) == $reader->id ? 'selected' : '' }}>
                        {{ $reader->name }}
                    </option>
                @endforeach
            </select>
            @error('reader_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Aizņemšanas datums</label>
            <input type="date" name="borrowed_at" value="{{ old('borrowed_at', $borrowing->borrowed_at->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required>
            @error('borrowed_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Atdošanas datums</label>
            <input type="date" name="returned_at" value="{{ old('returned_at', $borrowing->returned_at?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
            @error('returned_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Atjaunināt</button>
        <a href="{{ route('borrowings.index') }}" class="ml-2 text-gray-600 hover:underline">Atcelt</a>
    </form>
@endsection
