@extends('layouts.app')
@section('title', $book->title)

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ $book->title }}</h1>
    <div class="bg-white rounded shadow p-6 max-w-lg">
        <p><strong>ISBN:</strong> {{ $book->isbn }}</p>
        <p><strong>Pieejamie eksemplāri:</strong> {{ $book->available_copies }}</p>
        <p><strong>Izveidots:</strong> {{ $book->created_at }}</p>
        <p><strong>Atjaunināts:</strong> {{ $book->updated_at }}</p>
    </div>
    <div class="mt-4 flex gap-2">
        <a href="{{ route('books.edit', $book) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Labot</a>
        <a href="{{ route('books.index') }}" class="text-gray-600 hover:underline ml-2">Atpakaļ</a>
    </div>
@endsection
