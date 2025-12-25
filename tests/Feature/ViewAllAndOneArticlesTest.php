<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;
use App\Models\Article;
use App\Models\Revision;

class ViewAllAndOneArticlesTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_view_one_article_and_all_articles_on_wiki(): void
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
        ]);

        $response = $this->get("/wiki/{$wiki->url}/all-articles");

        $response->assertStatus(200);

        $response2 = $this->get("/wiki/{$wiki->url}/article/{$article->url_title}");

        $response2->assertStatus(200);
    }
}
