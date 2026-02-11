<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_create_article(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-article-creation-test',
        ]);

        $data = [
            'url_title' => 'test-article',
            'title' => 'Test Article',
            'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\n Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32."
        ];

        $response = $this->post("wiki/{$wiki->url}/store", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('articles', [
            'url_title' => $data['url_title'],
            'title' => $data['title'],
            'wiki_id' => $wiki->id,
        ]);
        $this->assertDatabaseHas('revisions', [
            'url_title' => $data['url_title'],
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => 0,
        ]);
    }

    public function test_that_registered_user_can_create_article(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test40@example.com',
        ]);

        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-article-creation-test',
        ]);

        $data = [
            'url_title' => 'test-article',
            'title' => 'Test Article',
            'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\n Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32."
        ];

        $this->actingAs($user);

        $response = $this->post("wiki/{$wiki->url}/store", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('articles', [
            'url_title' => $data['url_title'],
            'title' => $data['title'],
            'wiki_id' => $wiki->id,
        ]);
        $this->assertDatabaseHas('revisions', [
            'url_title' => $data['url_title'],
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => $user->id,
        ]);
    }
}
