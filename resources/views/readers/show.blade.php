@extends('layouts.app')
@section('title', $reader->name)

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ $reader->name }}</h1>
    <div class="bg-white rounded shadow p-6 max-w-lg">
        <p><strong>E-pasts:</strong> {{ $reader->email }}</p>
        <p><strong>Izveidots:</strong> {{ $reader->created_at }}</p>
        <p><strong>Atjaunināts:</strong> {{ $reader->updated_at }}</p>
    </div>
    <div class="mt-4 flex gap-2">
        <a href="{{ route('readers.edit', $reader) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Labot</a>
        <a href="{{ route('readers.index') }}" class="text-gray-600 hover:underline ml-2">Atpakaļ</a>
    </div>
@endsection
