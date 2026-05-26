<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->integer('old_available_copies');
            $table->integer('new_available_copies');
            $table->timestamp('changed_at');
        });

        DB::statement('
            CREATE TRIGGER IF NOT EXISTS log_available_copies_change
            AFTER UPDATE ON books
            FOR EACH ROW
            WHEN OLD.available_copies != NEW.available_copies
            BEGIN
                INSERT INTO book_logs (book_id, old_available_copies, new_available_copies, changed_at)
                VALUES (OLD.id, OLD.available_copies, NEW.available_copies, datetime(\'now\'));
            END;
        ');
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS log_available_copies_change');
        Schema::dropIfExists('book_logs');
    }
};
