<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'reader_id' => Reader::factory(),
            'borrowed_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'returned_at' => fake()->optional(0.7)->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
