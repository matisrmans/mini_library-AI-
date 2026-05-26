@extends('layouts.app')
@section('title', 'Pievienot grāmatu')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Pievienot grāmatu</h1>

    <form action="{{ route('books.store') }}" method="POST" class="bg-white rounded shadow p-6 max-w-lg">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">Nosaukums</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">ISBN</label>
            <input type="text" name="isbn" value="{{ old('isbn') }}" class="w-full border rounded px-3 py-2" required>
            @error('isbn') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Pieejamie eksemplāri</label>
            <input type="number" name="available_copies" value="{{ old('available_copies', 1) }}" class="w-full border rounded px-3 py-2" required>
            @error('available_copies') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Saglabāt</button>
        <a href="{{ route('books.index') }}" class="ml-2 text-gray-600 hover:underline">Atcelt</a>
    </form>
@endsection
