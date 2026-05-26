@extends('layouts.app')
@section('title', 'Aizņēmums')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Aizņēmums</h1>
    <div class="bg-white rounded shadow p-6 max-w-lg">
        <p><strong>Grāmata:</strong> {{ $borrowing->book->title }}</p>
        <p><strong>Lasītājs:</strong> {{ $borrowing->reader->name }}</p>
        <p><strong>Aizņemšanas datums:</strong> {{ $borrowing->borrowed_at }}</p>
        <p><strong>Atdošanas datums:</strong> {{ $borrowing->returned_at ?? 'Nav atdots' }}</p>
        <p><strong>Izveidots:</strong> {{ $borrowing->created_at }}</p>
        <p><strong>Atjaunināts:</strong> {{ $borrowing->updated_at }}</p>
    </div>
    <div class="mt-4 flex gap-2">
        <a href="{{ route('borrowings.edit', $borrowing) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Labot</a>
        <a href="{{ route('borrowings.index') }}" class="text-gray-600 hover:underline ml-2">Atpakaļ</a>
    </div>
@endsection
