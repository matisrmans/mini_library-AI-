<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_books()
    {
        Book::factory(3)->create();

        $response = $this->get(route('books.index'));

        $response->assertOk();
        $response->assertViewHas('books');
    }

    public function test_can_create_book()
    {
        $response = $this->get(route('books.create'));
        $response->assertOk();

        $response = $this->post(route('books.store'), [
            'title' => 'Testa grāmata',
            'isbn' => '978-3-16-148410-0',
            'available_copies' => 5,
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['isbn' => '978-3-16-148410-0']);
    }

    public function test_can_show_book()
    {
        $book = Book::factory()->create();

        $response = $this->get(route('books.show', $book));

        $response->assertOk();
        $response->assertViewHas('book');
    }

    public function test_can_update_book()
    {
        $book = Book::factory()->create();

        $response = $this->put(route('books.update', $book), [
            'title' => 'Atjaunināts',
            'isbn' => $book->isbn,
            'available_copies' => 3,
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['title' => 'Atjaunināts']);
    }

    public function test_can_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_validation_errors_on_store()
    {
        $response = $this->post(route('books.store'), [
            'title' => '',
            'isbn' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'isbn', 'available_copies']);
    }
}
