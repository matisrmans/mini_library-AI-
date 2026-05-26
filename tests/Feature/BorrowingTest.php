<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BorrowingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_borrowings()
    {
        Borrowing::factory(3)->create();

        $response = $this->get(route('borrowings.index'));

        $response->assertOk();
        $response->assertViewHas('borrowings');
    }

    public function test_can_create_borrowing()
    {
        $book = Book::factory()->create(['available_copies' => 5]);
        $reader = Reader::factory()->create();

        $response = $this->post(route('borrowings.store'), [
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'borrowed_at' => '2026-05-01',
        ]);

        $response->assertRedirect(route('borrowings.index'));
        $this->assertDatabaseHas('borrowings', ['book_id' => $book->id]);
    }

    public function test_borrowing_decrements_available_copies()
    {
        $book = Book::factory()->create(['available_copies' => 5]);
        $reader = Reader::factory()->create();

        $this->post(route('borrowings.store'), [
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'borrowed_at' => '2026-05-01',
        ]);

        $this->assertEquals(4, $book->fresh()->available_copies);
    }

    public function test_cannot_borrow_when_no_copies_available()
    {
        $book = Book::factory()->create(['available_copies' => 0]);
        $reader = Reader::factory()->create();

        $response = $this->post(route('borrowings.store'), [
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'borrowed_at' => '2026-05-01',
        ]);

        $response->assertSessionHasErrors('book_id');
        $this->assertDatabaseMissing('borrowings', ['book_id' => $book->id]);
        $this->assertEquals(0, $book->fresh()->available_copies);
    }

    public function test_atomic_borrowing_prevents_over_borrowing()
    {
        $book = Book::factory()->create(['available_copies' => 1]);
        $reader1 = Reader::factory()->create();
        $reader2 = Reader::factory()->create();

        $caught = 0;
        DB::transaction(function () use ($book, $reader1, $reader2, &$caught) {
            $bookRow = Book::where('id', $book->id)->lockForUpdate()->first();

            $this->post(route('borrowings.store'), [
                'book_id' => $book->id,
                'reader_id' => $reader1->id,
                'borrowed_at' => '2026-05-01',
            ])->assertRedirect(route('borrowings.index'));

            $bookRow = Book::where('id', $book->id)->first();
            $this->assertEquals(0, $bookRow->available_copies);

            $response2 = $this->post(route('borrowings.store'), [
                'book_id' => $book->id,
                'reader_id' => $reader2->id,
                'borrowed_at' => '2026-05-01',
            ]);
            $response2->assertSessionHasErrors('book_id');
        });

        $this->assertDatabaseCount('borrowings', 1);
        $this->assertEquals(0, $book->fresh()->available_copies);
    }

    public function test_can_show_borrowing()
    {
        $borrowing = Borrowing::factory()->create();

        $response = $this->get(route('borrowings.show', $borrowing));

        $response->assertOk();
        $response->assertViewHas('borrowing');
    }

    public function test_can_update_borrowing()
    {
        $borrowing = Borrowing::factory()->create(['returned_at' => null]);

        $response = $this->put(route('borrowings.update', $borrowing), [
            'book_id' => $borrowing->book_id,
            'reader_id' => $borrowing->reader_id,
            'borrowed_at' => '2026-05-01',
            'returned_at' => '2026-05-15',
        ]);

        $response->assertRedirect(route('borrowings.index'));
        $this->assertNotNull($borrowing->fresh()->returned_at);
    }

    public function test_returning_book_increments_available_copies()
    {
        $book = Book::factory()->create(['available_copies' => 0]);
        $reader = Reader::factory()->create();
        $borrowing = Borrowing::factory()->create([
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'returned_at' => null,
        ]);

        $this->assertEquals(0, $book->fresh()->available_copies);

        $this->put(route('borrowings.update', $borrowing), [
            'book_id' => $borrowing->book_id,
            'reader_id' => $borrowing->reader_id,
            'borrowed_at' => $borrowing->borrowed_at->format('Y-m-d'),
            'returned_at' => '2026-05-15',
        ]);

        $this->assertEquals(1, $book->fresh()->available_copies);
    }

    public function test_can_delete_borrowing()
    {
        $borrowing = Borrowing::factory()->create();

        $response = $this->delete(route('borrowings.destroy', $borrowing));

        $response->assertRedirect(route('borrowings.index'));
        $this->assertDatabaseMissing('borrowings', ['id' => $borrowing->id]);
    }

    public function test_deleting_unreturned_borrowing_increments_copies()
    {
        $book = Book::factory()->create(['available_copies' => 0]);
        $reader = Reader::factory()->create();
        $borrowing = Borrowing::factory()->create([
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'returned_at' => null,
        ]);

        $this->assertEquals(0, $book->fresh()->available_copies);

        $this->delete(route('borrowings.destroy', $borrowing));

        $this->assertEquals(1, $book->fresh()->available_copies);
    }

    public function test_validation_errors_on_store()
    {
        $response = $this->post(route('borrowings.store'), [
            'book_id' => 999,
            'reader_id' => 999,
            'borrowed_at' => '',
        ]);

        $response->assertSessionHasErrors(['book_id', 'reader_id', 'borrowed_at']);
    }
}
