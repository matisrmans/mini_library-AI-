<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookLog;
use App\Models\Borrowing;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_trigger_logs_changes_when_borrowing_book()
    {
        $book = Book::factory()->create(['available_copies' => 3]);
        $reader = Reader::factory()->create();

        $this->post(route('borrowings.store'), [
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'borrowed_at' => '2026-05-26',
        ]);

        $logs = BookLog::where('book_id', $book->id)->get();
        $this->assertCount(1, $logs);
        $this->assertEquals(3, $logs[0]->old_available_copies);
        $this->assertEquals(2, $logs[0]->new_available_copies);
    }

    public function test_trigger_logs_changes_when_returning_book()
    {
        $book = Book::factory()->create(['available_copies' => 0]);
        $reader = Reader::factory()->create();
        $borrowing = Borrowing::factory()->create([
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'returned_at' => null,
        ]);

        $this->put(route('borrowings.update', $borrowing), [
            'book_id' => $borrowing->book_id,
            'reader_id' => $borrowing->reader_id,
            'borrowed_at' => $borrowing->borrowed_at->format('Y-m-d'),
            'returned_at' => '2026-05-26',
        ]);

        $logs = BookLog::where('book_id', $book->id)->get();
        $this->assertCount(1, $logs);
        $this->assertEquals(0, $logs[0]->old_available_copies);
        $this->assertEquals(1, $logs[0]->new_available_copies);
    }

    public function test_trigger_logs_when_updating_book_directly_in_database()
    {
        $book = Book::factory()->create(['available_copies' => 5]);

        DB::statement('UPDATE books SET available_copies = 10 WHERE id = ?', [$book->id]);

        $logs = BookLog::where('book_id', $book->id)->get();
        $this->assertCount(1, $logs);
        $this->assertEquals(5, $logs[0]->old_available_copies);
        $this->assertEquals(10, $logs[0]->new_available_copies);
    }

    public function test_trigger_does_not_log_when_available_copies_unchanged()
    {
        $book = Book::factory()->create(['available_copies' => 5]);

        DB::statement('UPDATE books SET title = ? WHERE id = ?', ['New Title', $book->id]);

        $this->assertCount(0, BookLog::where('book_id', $book->id)->get());
    }

    public function test_trigger_fires_via_book_controller_update()
    {
        $book = Book::factory()->create(['available_copies' => 7]);

        $this->put(route('books.update', $book), [
            'title' => $book->title,
            'isbn' => $book->isbn,
            'available_copies' => 2,
        ]);

        $logs = BookLog::where('book_id', $book->id)->get();
        $this->assertCount(1, $logs);
        $this->assertEquals(7, $logs[0]->old_available_copies);
        $this->assertEquals(2, $logs[0]->new_available_copies);
    }
}
