<?php

namespace Database\Seeders;

use App\Models\Reader;
use Illuminate\Database\Seeder;

class ReaderSeeder extends Seeder
{
    public function run(): void
    {
        Reader::factory(20)->create();
    }
}
