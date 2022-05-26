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
            ->assertStatus(200); // 200 = OK
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
            ->assertStatus(200); // 200 = OK

        $this->assertDatabaseHas('posts', ['title' => $newTitle]);
    }

    public function test_delete()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('DELETE', "/api/posts/$post->id");

        $response->assertSee(null)->assertStatus(204); // 204 = No content

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {
        factory(Post::class, 5)->create();

        $response = $this->json('GET', '/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at']
            ]
        ])->assertStatus(200);
    }
}
