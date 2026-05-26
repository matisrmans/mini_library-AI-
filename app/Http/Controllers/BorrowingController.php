<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reader;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index()
    {
        $borrowings = Borrowing::with(['book', 'reader'])->latest()->paginate(10);
        return view('borrowings.index', compact('borrowings'));
    }

    public function create()
    {
        $books = Book::all();
        $readers = Reader::all();
        return view('borrowings.create', compact('books', 'readers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'reader_id' => 'required|exists:readers,id',
            'borrowed_at' => 'required|date',
        ]);

        $borrowing = Borrowing::create([
            'book_id' => $validated['book_id'],
            'reader_id' => $validated['reader_id'],
            'borrowed_at' => $validated['borrowed_at'],
        ]);

        $book = Book::find($validated['book_id']);
        if ($book->available_copies > 0) {
            $book->decrement('available_copies');
        }

        return redirect()->route('borrowings.index')->with('success', 'Aizņēmums reģistrēts!');
    }

    public function show(Borrowing $borrowing)
    {
        return view('borrowings.show', compact('borrowing'));
    }

    public function edit(Borrowing $borrowing)
    {
        $books = Book::all();
        $readers = Reader::all();
        return view('borrowings.edit', compact('borrowing', 'books', 'readers'));
    }

    public function update(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'reader_id' => 'required|exists:readers,id',
            'borrowed_at' => 'required|date',
            'returned_at' => 'nullable|date|after_or_equal:borrowed_at',
        ]);

        $borrowing->update($validated);

        return redirect()->route('borrowings.index')->with('success', 'Aizņēmums atjaunināts!');
    }

    public function destroy(Borrowing $borrowing)
    {
        $book = $borrowing->book;
        $borrowing->delete();

        if ($book && !$borrowing->returned_at) {
            $book->increment('available_copies');
        }

        return redirect()->route('borrowings.index')->with('success', 'Aizņēmums dzēsts!');
    }
}
