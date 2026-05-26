<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return DB::transaction(function () use ($validated) {
            $book = Book::where('id', $validated['book_id'])->lockForUpdate()->first();

            if (!$book || $book->available_copies < 1) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['book_id' => 'Grāmata nav pieejama aizņemšanai.']);
            }

            Borrowing::create([
                'book_id' => $validated['book_id'],
                'reader_id' => $validated['reader_id'],
                'borrowed_at' => $validated['borrowed_at'],
            ]);

            $book->decrement('available_copies');

            return redirect()->route('borrowings.index')->with('success', 'Aizņēmums reģistrēts!');
        });
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

        DB::transaction(function () use ($borrowing, $validated) {
            $wasReturned = $borrowing->returned_at !== null;

            if (($validated['returned_at'] ?? false) && !$wasReturned) {
                $book = Book::where('id', $borrowing->book_id)->lockForUpdate()->first();
                $borrowing->update($validated);
                if ($book) {
                    $book->increment('available_copies');
                }
            } else {
                $borrowing->update($validated);
            }
        });

        return redirect()->route('borrowings.index')->with('success', 'Aizņēmums atjaunināts!');
    }

    public function destroy(Borrowing $borrowing)
    {
        DB::transaction(function () use ($borrowing) {
            $book = Book::where('id', $borrowing->book_id)->lockForUpdate()->first();

            $borrowing->delete();

            if ($book && !$borrowing->returned_at) {
                $book->increment('available_copies');
            }
        });

        return redirect()->route('borrowings.index')->with('success', 'Aizņēmums dzēsts!');
    }
}
