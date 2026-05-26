<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_can_show_borrowing()
    {
        $borrowing = Borrowing::factory()->create();

        $response = $this->get(route('borrowings.show', $borrowing));

        $response->assertOk();
        $response->assertViewHas('borrowing');
    }

    public function test_can_update_borrowing()
    {
        $borrowing = Borrowing::factory()->create();

        $response = $this->put(route('borrowings.update', $borrowing), [
            'book_id' => $borrowing->book_id,
            'reader_id' => $borrowing->reader_id,
            'borrowed_at' => '2026-05-01',
            'returned_at' => '2026-05-15',
        ]);

        $response->assertRedirect(route('borrowings.index'));
        $this->assertNotNull($borrowing->fresh()->returned_at);
    }

    public function test_can_delete_borrowing()
    {
        $borrowing = Borrowing::factory()->create();

        $response = $this->delete(route('borrowings.destroy', $borrowing));

        $response->assertRedirect(route('borrowings.index'));
        $this->assertDatabaseMissing('borrowings', ['id' => $borrowing->id]);
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
