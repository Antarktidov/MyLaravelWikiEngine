<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;
use App\Models\Article;
use App\Models\Revision;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_update_article(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);
        $user = User::factory()->create();
        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
            'url_title' => $article->url_title,
            'title' => $article->title,
        ]);

        $data = [
            'title' => 'string1',
            'url_title' => 'string2',
            'content' => 'string3',
        ];

        $response = $this->post("/wiki/{$wiki->url}/update/{$article->url_title}/edit", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('revisions', [
            'title' => $data['title'],
            'url_title' => $data['url_title'],
            'user_id' => 0,
            'content' => $data['content'],
        ]);
    }
}
