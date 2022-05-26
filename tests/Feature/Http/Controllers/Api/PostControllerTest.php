<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store()
    {
        //$this->withoutExceptionHandling();

        $response = $this->json('POST', '/api/posts', [
            'title' => 'Post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'Post de prueba'])
            ->assertStatus(201); // 201 = Created

        $this->assertDatabaseHas('posts', ['title' => 'Post de prueba']);
    }

    public function test_validate_title()
    {
        $response = $this->json('POST', '/api/posts', [
            'title' => ''
        ]);

        // 422 = Unprocessable entity
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_show()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('GET', "/api/posts/$post->id");

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200); // 200 = Accepted
    }

    public function test_404_show()
    {
        $response = $this->json('GET', '/api/posts/1000');
        $response->assertStatus(404); // 404 = Not found
    }

    public function test_update()
    {
        //$this->withoutExceptionHandling();

        $post = factory(Post::class)->create();
        $newTitle = 'Título nuevo';

        $response = $this->json('PUT', "/api/posts/$post->id", [
            'title' => $newTitle
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $newTitle])
            ->assertStatus(200);

        $this->assertDatabaseHas('posts', ['title' => $newTitle]);
    }
}
