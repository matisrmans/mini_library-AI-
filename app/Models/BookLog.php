<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'old_available_copies',
        'new_available_copies',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
