<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;
use App\Models\Article;
use App\Models\Revision;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class ViewDeletedArticleTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_not_view_deleted_article_and_its_history(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-article-deletion-test',
        ]);

        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
            'deleted_at' => '2025-12-01 21:55:38',
        ]);

        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'url_title' => $article->url_title,
            'title' => $article->title,
            'user_id' => 0,
            'user_ip' => '127.0.0.1',
        ]);

        $response = $this->get("/wiki/{$wiki->url}/trash/article/{$article->url_title}");

        $response->assertStatus(401);

        $response2 = $this->get("/wiki/{$wiki->url}/trash/article/{$article->url_title}/history");

        $response2->assertStatus(401);
    }
}
