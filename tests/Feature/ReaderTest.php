<?php

namespace Tests\Feature;

use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_readers()
    {
        Reader::factory(3)->create();

        $response = $this->get(route('readers.index'));

        $response->assertOk();
        $response->assertViewHas('readers');
    }

    public function test_can_create_reader()
    {
        $response = $this->get(route('readers.create'));
        $response->assertOk();

        $response = $this->post(route('readers.store'), [
            'name' => 'Jānis Bērziņš',
            'email' => 'janis@example.com',
        ]);

        $response->assertRedirect(route('readers.index'));
        $this->assertDatabaseHas('readers', ['email' => 'janis@example.com']);
    }

    public function test_can_show_reader()
    {
        $reader = Reader::factory()->create();

        $response = $this->get(route('readers.show', $reader));

        $response->assertOk();
        $response->assertViewHas('reader');
    }

    public function test_can_update_reader()
    {
        $reader = Reader::factory()->create();

        $response = $this->put(route('readers.update', $reader), [
            'name' => 'Atjaunināts',
            'email' => $reader->email,
        ]);

        $response->assertRedirect(route('readers.index'));
        $this->assertDatabaseHas('readers', ['name' => 'Atjaunināts']);
    }

    public function test_can_delete_reader()
    {
        $reader = Reader::factory()->create();

        $response = $this->delete(route('readers.destroy', $reader));

        $response->assertRedirect(route('readers.index'));
        $this->assertDatabaseMissing('readers', ['id' => $reader->id]);
    }

    public function test_validation_errors_on_store()
    {
        $response = $this->post(route('readers.store'), [
            'name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }
}
