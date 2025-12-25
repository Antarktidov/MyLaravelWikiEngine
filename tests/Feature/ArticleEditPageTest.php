<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\User;
use App\Models\Revision;

class ArticleEditPageTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_asses_edit_page(): void
    {
        $wiki = Wiki::factory()->create();

        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);

        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'url_title' => $article->url_title,
            'title' => $article->title,
            'user_id' => 0,
            'user_ip' => '127.0.0.1',
        ]);

        $response = $this->get("/wiki/{$wiki->url}/article/{$article->url_title}/edit");

        $response->assertStatus(200);
    }
}
