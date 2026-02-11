<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\User;
use App\Models\Revision;

class ArticleHistoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_example(): void
    {
        $wiki = Wiki::factory()->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);

        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'url_title' => $article->url_title,
            'title' => $article->title,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
            'is_approved' => true,
        ]);

        $response = $this->get("/wiki/{$wiki->url}/article/{$article->url_title}/history");

        $response->assertStatus(200);
    }
}
